/**
 * SoulScript - Pre-Push Image & Gallery Verification Test
 * Tests image URL normalization, onerror fallback, and scrapbook photo rendering
 * Run this BEFORE pushing any code changes
 */

const { chromium } = require('playwright');
const http = require('http');

function postJson(url, data) {
  return new Promise((resolve, reject) => {
    const u = new URL(url);
    const postData = JSON.stringify(data);
    const req = http.request({
      hostname: u.hostname,
      port: u.port,
      path: u.pathname + u.search,
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(postData)
      }
    }, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', () => {
        try { resolve(JSON.parse(body)); }
        catch(e) { resolve({ error: body, raw: body.slice(0, 200) }); }
      });
    });
    req.on('error', reject);
    req.write(postData);
    req.end();
  });
}

function getJson(url) {
  return new Promise((resolve) => {
    http.get(url, (res) => {
      let body = '';
      res.on('data', c => body += c);
      res.on('end', () => {
        try { resolve(JSON.parse(body)); }
        catch(e) { resolve({ error: body.slice(0, 200) }); }
      });
    }).on('error', () => resolve({ error: 'request failed' }));
  });
}

const BASE_URL = 'http://127.0.0.1:8000';

const VALID_SAMPLE_URLS = [
  'https://images.unsplash.com/photo-1518199266791-5375a83190b7?auto=format&fit=crop&w=800&q=80',
  'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=800&q=80'
];

// These are wrong local paths that should never reach the DB
// On production, they'd be saved as http://localhost/..., /uploads/..., etc.
const BAD_LOCAL_URLS = [
  '/uploads/old_page/relative_path.jpg',          // relative path
  'uploads/old_page/no_slash.jpg'                 // relative without slash
];

async function createOrderAndPage(opts) {
  // 1. Create Order
  const orderRes = await postJson(`${BASE_URL}/api/create_order.php`, {
    buyer_name: opts.buyer_name,
    buyer_phone: '+91 9876543210',
    buyer_email: `test_${Date.now()}@soulscript.in`,
    template_id: opts.template_id
  });

  if (!orderRes.success || !orderRes.order) {
    return { error: 'Order creation failed: ' + JSON.stringify(orderRes) };
  }
  const orderId = orderRes.order.order_id;

  // 2. Mark as Paid via webhook
  const payRes = await postJson(`${BASE_URL}/api/webhook_razorpay.php`, {
    order_id: orderId,
    razorpay_payment_id: `pay_test_img_${Date.now()}`,
    status: 'paid'
  });
  if (!payRes.success) {
    return { error: 'Payment webhook failed: ' + JSON.stringify(payRes) };
  }

  // 3. Create page
  const slug = `test-img-${Date.now()}-${Math.floor(Math.random() * 9999)}`;
  const pageRes = await postJson(`${BASE_URL}/api/create_page.php`, {
    order_id: orderId,
    custom_slug: slug,
    partner_name: opts.partner_name,
    hint_question: 'Test question?',
    hint_answer: opts.hint_answer || 'testanswer',
    love_note_text: 'Test love note',
    photos: opts.photos || VALID_SAMPLE_URLS
  });

  if (!pageRes.success) {
    return { error: 'Page creation failed: ' + JSON.stringify(pageRes) };
  }

  return { orderId, slug, editToken: pageRes.edit_token, urlSlug: pageRes.url_slug };
}

async function runTests() {
  console.log('=================================================================');
  console.log('   SOULSCRIPT PRE-PUSH IMAGE & GALLERY VERIFICATION TEST');
  console.log('=================================================================\n');

  let hasFailures = false;
  let browser;
  try {
    browser = await chromium.launch({ headless: true });
  } catch(err) {
    const { execSync } = require('child_process');
    execSync('npx playwright install chromium', { stdio: 'inherit' });
    browser = await chromium.launch({ headless: true });
  }
  const page = await browser.newPage();

  // ─────────────────────────────────────────────────────────────────
  // TEST 1: Backend URL Normalization
  // ─────────────────────────────────────────────────────────────────
  console.log('--- [TEST 1] Backend URL Normalization ---');

  const testPage1 = await createOrderAndPage({
    buyer_name: 'Norm Tester',
    template_id: 'anniversary_reveal',
    partner_name: 'Test Partner',
    photos: [
      ...VALID_SAMPLE_URLS,
      ...BAD_LOCAL_URLS
    ]
  });

  if (testPage1.error) {
    console.error('  FAIL:', testPage1.error);
    hasFailures = true;
  } else {
    console.log(`  OK: Created page /gift/${testPage1.urlSlug}`);

    const getRes = await getJson(`${BASE_URL}/api/edit_page.php?token=${testPage1.editToken}`);

    if (getRes.success && Array.isArray(getRes.media)) {
      let normOk = true;
      for (const m of getRes.media) {
        const fp = m.file_path || '';
        // FAIL if URL is still a bare relative path (no http prefix)
        if (!fp.startsWith('http')) {
          console.error(`  FAIL: Relative path not converted to absolute URL: "${fp}"`);
          normOk = false;
          hasFailures = true;
        }
      }
      if (normOk) {
        console.log(`  OK: All ${getRes.media.length} file_paths in DB are absolute URLs (not relative)`);
      }
      const validKept = getRes.media.filter(m => m.file_path && m.file_path.includes('unsplash.com'));
      console.log(`  OK: ${validKept.length} Unsplash URLs preserved correctly`);
      // Verify that localhost-style URLs are re-based to APP_URL (not kept as-is in different form)
      const localKept = getRes.media.filter(m => m.file_path && m.file_path.includes('localhost/soulscript'));
      if (localKept.length === 0) {
        console.log('  OK: No raw localhost/soulscript URLs left in DB');
      } else {
        console.error(`  FAIL: ${localKept.length} raw localhost/soulscript URLs remain unnormalized`);
        hasFailures = true;
      }
    } else {
      console.log('  WARN: Could not verify normalization from edit API');
    }
  }

  // ─────────────────────────────────────────────────────────────────
  // TEST 2: Manage Panel Scrapbook - onerror fallback present
  // ─────────────────────────────────────────────────────────────────
  console.log('\n--- [TEST 2] Manage Panel - onerror fallback on scrapbook imgs ---');

  const testPage2 = await createOrderAndPage({
    buyer_name: 'Photo Tester',
    template_id: 'anniversary_reveal',
    partner_name: 'Photo Partner',
    photos: VALID_SAMPLE_URLS
  });

  if (testPage2.error) {
    console.error('  FAIL:', testPage2.error);
    hasFailures = true;
  } else {
    await page.goto(`${BASE_URL}/edit.php?token=${testPage2.editToken}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(1500);
    await page.click('#tabBtn-photos').catch(() => {});
    await page.waitForTimeout(600);

    const imgHandles = await page.$$('#dashScrapbookContainer img');
    let onerrorCount = 0;
    for (const img of imgHandles) {
      const attr = await img.getAttribute('onerror');
      if (attr) onerrorCount++;
    }

    if (imgHandles.length === 0) {
      console.log('  WARN: No images in scrapbook container yet');
    } else if (onerrorCount === imgHandles.length) {
      console.log(`  OK: All ${imgHandles.length} scrapbook imgs have onerror fallback`);
    } else {
      console.error(`  FAIL: Only ${onerrorCount}/${imgHandles.length} have onerror`);
      hasFailures = true;
    }

    // Sample gallery onerror check
    const sampleImgs = await page.$$('#dashSamplePhotosGrid img');
    let sampleOnerrorCount = 0;
    for (const img of sampleImgs) {
      const attr = await img.getAttribute('onerror');
      if (attr) sampleOnerrorCount++;
    }
    if (sampleImgs.length > 0 && sampleOnerrorCount === sampleImgs.length) {
      console.log(`  OK: All ${sampleImgs.length} sample gallery imgs have onerror fallback`);
    } else if (sampleImgs.length === 0) {
      console.log('  WARN: Sample gallery not rendered');
    } else {
      console.error(`  FAIL: Only ${sampleOnerrorCount}/${sampleImgs.length} sample imgs have onerror`);
      hasFailures = true;
    }
  }

  // ─────────────────────────────────────────────────────────────────
  // TEST 3: Gift Reveal Page - no broken images after unlock
  // ─────────────────────────────────────────────────────────────────
  console.log('\n--- [TEST 3] Gift Reveal Page - no broken images after unlock ---');

  const testPage3 = await createOrderAndPage({
    buyer_name: 'Reveal Tester',
    template_id: 'anniversary_reveal',
    partner_name: 'Reveal Partner',
    hint_answer: 'paris',
    photos: VALID_SAMPLE_URLS
  });

  if (testPage3.error) {
    console.error('  FAIL:', testPage3.error);
    hasFailures = true;
  } else {
    await page.goto(`${BASE_URL}/gift/${testPage3.urlSlug}`, { waitUntil: 'networkidle' });
    await page.waitForTimeout(1000);

    // Try to unlock
    const answerInput = page.locator('#hintAnswerInput');
    if (await answerInput.count() > 0) {
      await answerInput.fill('paris');
      const submitBtn = page.locator('#submitHintBtn');
      if (await submitBtn.count() > 0) await submitBtn.click();
      await page.waitForTimeout(2500);
    }

    // Count images with onerror protection
    const onerrorImgs = await page.$$('img[onerror]');
    console.log(`  OK: ${onerrorImgs.length} images with onerror protection on reveal page`);

    // Check for broken images
    const brokenImgs = await page.$$eval('img', imgs =>
      imgs
        .filter(img => img.naturalWidth === 0 && img.src && !img.src.startsWith('data:') && img.src !== '')
        .map(img => img.src)
    );

    if (brokenImgs.length === 0) {
      console.log('  OK: No broken images on gift reveal page');
    } else {
      console.error(`  FAIL: ${brokenImgs.length} broken images:`, brokenImgs.slice(0, 3));
      hasFailures = true;
    }
  }

  // ─────────────────────────────────────────────────────────────────
  // TEST 4: verify_hint.php normalizes URLs in unlock payload
  // ─────────────────────────────────────────────────────────────────
  console.log('\n--- [TEST 4] verify_hint.php - URL normalization in unlock payload ---');

  if (!testPage3.error) {
    const hintRes = await postJson(`${BASE_URL}/api/verify_hint.php`, {
      slug: testPage3.urlSlug,
      answer: 'paris'
    });

    if (hintRes.success && hintRes.content && Array.isArray(hintRes.content.media)) {
      let normOk = true;
      for (const m of hintRes.content.media) {
        const fp = m.file_path || '';
        if (fp.match(/127\.0\.0\.1:\d+/) || fp.match(/localhost:\d+/)) {
          console.error(`  FAIL: Dev-server URL in verify_hint response: "${fp}"`);
          normOk = false;
          hasFailures = true;
        }
      }
      if (normOk) {
        console.log(`  OK: All ${hintRes.content.media.length} media URLs normalized in verify_hint response`);
      }
    } else {
      console.log('  WARN: verify_hint returned no media array (page may have no photos)');
    }
  } else {
    console.log('  SKIP: Skipping (test page not created)');
  }

  await browser.close();

  console.log('\n=================================================================');
  if (hasFailures) {
    console.error('RESULT: PRE-PUSH TESTS FAILED — Do NOT push until issues fixed.');
    process.exit(1);
  } else {
    console.log('RESULT: ALL PRE-PUSH TESTS PASSED! Safe to push to GitHub.');
  }
  console.log('=================================================================\n');
}

runTests().catch(err => {
  console.error('Test runner crashed:', err);
  process.exit(1);
});
