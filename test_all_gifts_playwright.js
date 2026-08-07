const { chromium } = require('playwright');
const http = require('http');

// Helper to make HTTP POST requests to API
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
        try {
          resolve(JSON.parse(body));
        } catch(e) {
          resolve({ error: body });
        }
      });
    });
    req.on('error', reject);
    req.write(postData);
    req.end();
  });
}

const BASE_URL = 'http://127.0.0.1:8000';

const GIFTS_TO_TEST = [
  {
    template_id: 'anniversary_reveal',
    name: 'Anniversary Reveal',
    partner_name: 'Ananya (Anniversary)',
    hint_question: 'Our favorite anniversary cafe?',
    hint_answer: 'shimla',
    expectedTabTitle: '📍 Story Road Milestones',
    expectedBadgeText: 'Managing: Anniversary Reveal Plan'
  },
  {
    template_id: 'birthday_magic',
    name: 'Birthday Magic',
    partner_name: 'Rohan (Birthday)',
    hint_question: 'My secret nickname for you?',
    hint_answer: 'sunshine',
    expectedTabTitle: '🎂 Birthday & Reasons',
    expectedBadgeText: 'Managing: Birthday Magic Plan'
  },
  {
    template_id: 'perfect_proposal',
    name: 'Perfect Proposal',
    partner_name: 'Priya (Proposal)',
    hint_question: 'City where we fell in love?',
    hint_answer: 'paris',
    expectedTabTitle: '💍 Love Letter & Answer',
    expectedBadgeText: 'Managing: Perfect Proposal Plan'
  },
  {
    template_id: 'long_distance_love',
    name: 'Long Distance Love',
    partner_name: 'Aman (LDR)',
    hint_question: 'Our favorite reunion food?',
    hint_answer: 'pizza',
    expectedTabTitle: '🌍 Cities & Reunion Date',
    expectedBadgeText: 'Managing: Long Distance Love Plan'
  }
];

(async () => {
  console.log("================================================================");
  console.log("   SOULSCRIPT PLAYWRIGHT DEEP AUDIT: 4 GIFTS & MANAGE PANEL SYNC ");
  console.log("================================================================\n");

  let browser;
  let hasFailures = false;

  try {
    browser = await chromium.launch({ headless: true });
  } catch (err) {
    console.log("Chromium binary missing, attempting installation...");
    const { execSync } = require('child_process');
    execSync('npx playwright install chromium', { stdio: 'inherit' });
    browser = await chromium.launch({ headless: true });
  }

  const context = await browser.newContext();
  const page = await context.newPage();

  for (let i = 0; i < GIFTS_TO_TEST.length; i++) {
    const gift = GIFTS_TO_TEST[i];
    console.log(`\n--- [GIFT ${i+1}/4] TESTING TEMPLATE: ${gift.name} (${gift.template_id}) ---`);

    // 1. Create Order
    const orderRes = await postJson(`${BASE_URL}/api/create_order.php`, {
      buyer_name: `Buyer for ${gift.name}`,
      buyer_phone: `+91 98765432${i}0`,
      buyer_email: `buyer_${gift.template_id}@soulscript.in`,
      template_id: gift.template_id
    });

    if (!orderRes.success) {
      console.error(`❌ Order creation failed for ${gift.template_id}:`, orderRes);
      hasFailures = true;
      continue;
    }
    const orderId = orderRes.order.order_id;
    console.log(`  ✓ Created Order ID: ${orderId}`);

    // 2. Mark Order as Paid
    const payRes = await postJson(`${BASE_URL}/api/webhook_razorpay.php`, {
      order_id: orderId,
      razorpay_payment_id: `pay_test_pw_${Date.now()}_${i}`,
      status: 'paid'
    });
    if (!payRes.success) {
      console.error(`❌ Webhook payment failed for ${gift.template_id}:`, payRes);
      hasFailures = true;
      continue;
    }
    console.log(`  ✓ Webhook marked order as PAID`);

    // 3. Create Surprise Page
    const slug = `test-${gift.template_id.replace(/_/g, '-')}-${Date.now().toString().slice(-5)}`;
    const pageRes = await postJson(`${BASE_URL}/api/create_page.php`, {
      order_id: orderId,
      custom_slug: slug,
      partner_name: gift.partner_name,
      hint_question: gift.hint_question,
      hint_answer: gift.hint_answer,
      love_note_text: `Special love note for ${gift.partner_name}!`
    });

    if (!pageRes.success) {
      console.error(`❌ Page creation failed for ${gift.template_id}:`, pageRes);
      hasFailures = true;
      continue;
    }
    const editToken = pageRes.edit_token;
    console.log(`  ✓ Created Gift Page: /gift/${slug}`);
    console.log(`  ✓ Edit Token: ${editToken}`);

    // 4. Test Management Panel Adaptation in Playwright Browser
    const editUrl = `${BASE_URL}/edit.php?token=${editToken}`;
    await page.goto(editUrl, { waitUntil: 'networkidle' });
    await page.waitForTimeout(1000);

    // Check Plan Badge text in Manage Panel
    const badgeText = await page.locator('#activePlanBadge').innerText();
    if (badgeText.toLowerCase().includes(gift.expectedBadgeText.toLowerCase())) {
      console.log(`  ✅ Manage Panel Plan Badge matches: "${badgeText.trim()}"`);
    } else {
      console.error(`  ❌ Manage Panel Plan Badge mismatch! Expected "${gift.expectedBadgeText}", got "${badgeText}"`);
      hasFailures = true;
    }

    // Check Dynamic Theme Tab Title in Manage Panel
    const tabTitle = await page.locator('#tabBtn-theme').innerText();
    const cleanTab = tabTitle.replace('&amp;', '&').replace(/\s+/g, ' ').trim();
    const cleanExp = gift.expectedTabTitle.replace('&amp;', '&').replace(/\s+/g, ' ').trim();
    if (cleanTab.toLowerCase().includes(cleanExp.toLowerCase()) || cleanExp.toLowerCase().includes(cleanTab.toLowerCase())) {
      console.log(`  ✅ Manage Panel Theme Tab Title matches: "${tabTitle.trim()}"`);
    } else {
      console.error(`  ❌ Manage Panel Theme Tab mismatch! Expected "${gift.expectedTabTitle}", got "${tabTitle}"`);
      hasFailures = true;
    }

    // Check Letters & Tokens Tabs Visibility based on Template
    const isLettersHidden = await page.locator('#tabBtn-letters').evaluate(el => el.classList.contains('hidden'));
    const isTokensHidden = await page.locator('#tabBtn-tokens').evaluate(el => el.classList.contains('hidden'));

    if (gift.template_id === 'anniversary_reveal') {
      if (!isLettersHidden && !isTokensHidden) {
        console.log(`  ✅ Sealed Letters & Love Tokens tabs are VISIBLE for Anniversary Reveal`);
      } else {
        console.error(`  ❌ Sealed Letters / Tokens tabs should be visible for Anniversary Reveal!`);
        hasFailures = true;
      }
    } else {
      if (isLettersHidden && isTokensHidden) {
        console.log(`  ✅ Sealed Letters & Love Tokens tabs are correctly HIDDEN for ${gift.name}`);
      } else {
        console.error(`  ❌ Sealed Letters / Tokens tabs should be HIDDEN for ${gift.name}!`);
        hasFailures = true;
      }
    }

    // 5. Update partner name & details in Manage Panel and save
    const updatedPartnerName = `${gift.partner_name} UPDATED`;
    await page.locator('#partnerName').fill(updatedPartnerName);
    await page.locator('#loveNoteText').fill(`Updated note for ${updatedPartnerName}!`);
    
    // Click Save Button in Manage Panel
    await page.locator('button:has-text("Save All Changes")').click();
    await page.waitForTimeout(1000);
    console.log(`  ✓ Saved updates in Manage Panel for ${gift.name}`);

    // 6. Test Lock Screen & Gift Reveal Sync in Playwright Browser
    const giftUrl = `${BASE_URL}/gift/${slug}`;
    await page.goto(giftUrl, { waitUntil: 'networkidle' });

    // Solve Lock Screen
    const lockQuestion = await page.locator('#lockHintQuestion').innerText();
    if (lockQuestion.includes(gift.hint_question)) {
      console.log(`  ✅ Lock Screen Question matches: "${lockQuestion.trim()}"`);
    } else {
      console.error(`  ❌ Lock Screen Question mismatch! Expected "${gift.hint_question}", got "${lockQuestion}"`);
      hasFailures = true;
    }

    // Enter answer and submit
    await page.locator('#answerInput').fill(gift.hint_answer);
    await page.locator('button:has-text("Unlock Surprise Page")').click();
    await page.waitForTimeout(1000);

    // Verify Gift Page Content rendered with updated partner name
    const pageContentText = await page.locator('#resultContentContainer').innerText();
    if (pageContentText.includes(updatedPartnerName)) {
      console.log(`  ✅ Gift Reveal Page rendered updated partner name: "${updatedPartnerName}"`);
    } else {
      console.error(`  ❌ Gift Reveal Page missing updated partner name "${updatedPartnerName}"!`);
      hasFailures = true;
    }
  }

  await browser.close();

  console.log("\n================================================================");
  if (hasFailures) {
    console.log("❌ PLAYWRIGHT AUDIT COMPLETED WITH ISSUES!");
    process.exit(1);
  } else {
    console.log("🎉 PLAYWRIGHT AUDIT PASSED 100%! ALL 4 GIFTS & MANAGE PANELS ARE FULLY SYNCHRONIZED!");
    process.exit(0);
  }
})();
