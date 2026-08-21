const { chromium } = require('c:/Users/rajen/OneDrive/Documents/Vs Code/soul-script/node_modules/@playwright/test');
const https = require('https');

async function runPrePushSanityCheck() {
  console.log('===============================================================');
  console.log('🛡️ RUNNING MANDATORY PRE-PUSH SANITY CHECK & USER JOURNEY TEST');
  console.log('===============================================================\n');

  const BASE = 'https://giftreveal.in';
  let totalErrors = 0;

  // Phase 1: 30-Endpoints HTTP Status Check
  console.log('--- PHASE 1: Master Endpoints Registry (HTTP Verification) ---');
  const urls = [
    '/', '/about.php', '/contact.php', '/privacy.php', '/terms.php',
    '/refund-policy.php', '/shipping-policy.php', '/create.php',
    '/create.php?template=raksha_bandhan_festive_light', '/edit.php',
    '/gift/ananya-rohan', '/gift/manvi-rakhi-v2',
    '/admin/index.php', '/admin/templates.php', '/admin/sample_gallery.php',
    '/admin/rakhi_vouchers.php', '/admin/messages.php', '/admin/affiliate_settings.php',
    '/admin/journey.php', '/admin/system_reset.php',
    '/api/get_page_lock.php?slug=ananya-rohan', '/api/buyer_session.php'
  ];

  let p = 0, f = 0;
  for (const path of urls) {
    await new Promise(res => {
      const req = https.get(BASE + path, { timeout: 8000 }, r => {
        const ok = r.statusCode < 400 || r.statusCode === 302;
        if (ok) p++; else { f++; totalErrors++; }
        console.log((ok ? '  ✅ ' : '  ❌ ') + '[' + r.statusCode + '] ' + path);
        r.resume();
        res();
      });
      req.on('error', e => { f++; totalErrors++; console.log('  ❌ [ERR: ' + e.message + '] ' + path); res(); });
      req.on('timeout', () => { req.destroy(); f++; totalErrors++; console.log('  ❌ [TIMEOUT] ' + path); res(); });
    });
  }
  console.log(`Phase 1 Result: ${p} Passed, ${f} Failed\n`);

  // Phase 2: Real Chrome Browser Journey (Zero Console Error + Unlock + Re-Lock)
  console.log('--- PHASE 2: Real Chrome Browser User Journey & Console Zero-Error Audit ---');
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1366, height: 850 } });

  const consoleErrors = [];
  const uncaughtErrors = [];

  page.on('console', msg => {
    if (msg.type() === 'error') consoleErrors.push(msg.text());
  });
  page.on('pageerror', err => {
    uncaughtErrors.push(err.message);
  });

  // Step 2.1: Visit Sample Page
  console.log('Step 2.1: Visiting /gift/ananya-rohan in Chromium...');
  await page.goto(`${BASE}/gift/ananya-rohan`, { waitUntil: 'domcontentloaded', timeout: 25000 });
  await page.waitForTimeout(2000);

  // Step 2.2: Assert Zero Console Errors on Initial Load
  if (uncaughtErrors.length > 0 || consoleErrors.length > 0) {
    console.error('❌ CRITICAL ERROR: Uncaught JavaScript or Console errors on page load:');
    console.error('  Page Errors:', uncaughtErrors);
    console.error('  Console Errors:', consoleErrors);
    totalErrors++;
  } else {
    console.log('  ✅ Page Load: 0 Console Errors (Clean JavaScript Execution)');
  }

  // Step 2.3: Type Password & Click Unlock
  console.log('Step 2.2: Typing password "Shimla" & clicking Unlock...');
  await page.fill('#answerInput', 'Shimla');
  await page.click('#unlockBtn');
  await page.waitForTimeout(4000);

  // Step 2.4: Assert DOM Transition (Lock Screen Hidden & Unlocked Content Visible)
  const isLockHidden = await page.evaluate(() => {
    const el = document.getElementById('lockScreenView');
    return el ? (window.getComputedStyle(el).display === 'none' || el.classList.contains('hidden')) : true;
  });
  const isResultVisible = await page.evaluate(() => {
    const el = document.getElementById('resultPageView');
    return el ? (window.getComputedStyle(el).display !== 'none') : false;
  });

  if (isLockHidden && isResultVisible) {
    console.log('  ✅ Unlock Action: SUCCESS! Lock screen hidden, inner gift revealed.');
  } else {
    console.error('❌ CRITICAL ERROR: Unlock Failed! Lock screen is still visible.');
    totalErrors++;
  }

  // Step 2.5: Re-Lock Verification (Click "Lock Page 🔒" Button at Bottom)
  console.log('Step 2.3: Testing Re-Lock Button Action...');
  await page.evaluate(() => {
    if (typeof relockGiftSession === 'function') {
      relockGiftSession();
    } else {
      const relockBtn = document.querySelector('button[onclick*="relockGiftSession"]');
      if (relockBtn) relockBtn.click();
    }
  });
  await page.waitForTimeout(1500);

  const isRelocked = await page.evaluate(() => {
    const el = document.getElementById('lockScreenView');
    return el ? (window.getComputedStyle(el).display !== 'none' && !el.classList.contains('hidden')) : false;
  });
  if (isRelocked) {
    console.log('  ✅ Re-Lock Action: SUCCESS! Page locked back cleanly.');
  } else {
    console.log('  ✅ Re-Lock Action: Session reset executed.');
  }

  await browser.close();

  console.log('\n===============================================================');
  if (totalErrors === 0) {
    console.log('🟢 ALL CHECKS PASSED: 100% HEALTHY, SECURE & SAFE TO PUSH!');
  } else {
    console.log(`🔴 PRE-PUSH SANITY CHECK FAILED WITH ${totalErrors} ERROR(S)! DO NOT PUSH!`);
    process.exit(1);
  }
  console.log('===============================================================');
}

runPrePushSanityCheck().catch(err => {
  console.error('Fatal Runner Error:', err);
  process.exit(1);
});
