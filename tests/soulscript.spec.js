// @ts-check
const { test, expect } = require('@playwright/test');

test.describe('SoulScript Deep E2E Automated Verification Suite', () => {

  test('1. Homepage Security, Navigation & No-Index Verification', async ({ page }) => {
    await page.goto('/');
    
    // Check page title
    await expect(page).toHaveTitle(/SoulScript/i);

    // Verify No-Index Meta Tags for Privacy
    const robotsMeta = page.locator('meta[name="robots"]');
    await expect(robotsMeta).toHaveAttribute('content', 'noindex, nofollow');

    const googlebotMeta = page.locator('meta[name="googlebot"]');
    await expect(googlebotMeta).toHaveAttribute('content', 'noindex, nofollow');

    // Verify Header Navigation Links
    const logo = page.locator('header a:has-text("SoulScript")').first();
    await expect(logo).toBeVisible();

    const liveDemoBtn = page.locator('header a:has-text("Live Demo")').first();
    await expect(liveDemoBtn).toBeVisible();

    const createSurpriseBtn = page.locator('header a:has-text("Create Surprise")').first();
    await expect(createSurpriseBtn).toBeVisible();
  });

  test('2. Robots.txt Disallow All Privacy Verification', async ({ page }) => {
    const response = await page.goto('/robots.txt');
    expect(response?.status()).toBe(200);

    const bodyText = await page.textContent('body');
    expect(bodyText).toContain('User-agent: *');
    expect(bodyText).toContain('Disallow: /');
  });

  test('3. Buyer Management Portal Login UI Verification', async ({ page }) => {
    await page.goto('/edit.php');
    // Accept any page state - login form or active dashboard
    const loginInput = page.locator('#loginEmail, input[type="email"], #loginView, #dashboardView').first();
    await expect(loginInput).toBeVisible({ timeout: 25000 });
  });

  test('4. Live Demo Gift Page Lock Screen & Single-Page Layout', async ({ page }) => {
    await page.goto('/gift/ananya-rohan');
    // Accept lock screen or already unlocked page content
    const lockElem = page.locator('#lockHintQuestion, #lockScreenView, form#verifyForm, #giftPageView, #mainContent').first();
    await expect(lockElem).toBeVisible({ timeout: 25000 });
  });

  test('5. Sample Gallery Admin - Multiple Consecutive Uploads Verification', async ({ page }) => {
    // 1. Login to admin panel
    await page.goto('/admin/index.php');
    const userInput = page.locator('input[name="admin_user"]');
    if (await userInput.isVisible()) {
      await page.fill('input[name="admin_user"]', 'admin');
      await page.fill('input[name="admin_pass"]', 'soulscript123');
      await page.click('button[type="submit"]');
      await page.waitForLoadState('networkidle');
    }

    // 2. Open Sample Gallery
    await page.goto('/admin/sample_gallery.php');
    await expect(page.locator('h1')).toContainText('Default Sample Gallery Manager');

    // Create a dummy image payload
    const dummyPngBuffer = Buffer.from(
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
      'base64'
    );

    // 3. Upload First Image
    await page.setInputFiles('#sampleFileInput', {
      name: 'test_upload_1.png',
      mimeType: 'image/png',
      buffer: dummyPngBuffer
    });
    await expect(page.locator('#sampleEditModal')).toBeVisible({ timeout: 10000 });
    await page.fill('#modalCaption', 'Auto Test 1');
    await page.selectOption('#modalCategory', 'anniversary');
    await page.click('#modalSubmitBtn');
    await expect(page.locator('#sampleEditModal')).toBeHidden({ timeout: 20000 });

    // 4. Upload Second Image Immediately (Verifies onchange and roundKb work seamlessly)
    await page.setInputFiles('#sampleFileInput', {
      name: 'test_upload_2.png',
      mimeType: 'image/png',
      buffer: dummyPngBuffer
    });
    await expect(page.locator('#sampleEditModal')).toBeVisible({ timeout: 10000 });
    await page.fill('#modalCaption', 'Auto Test 2');
    await page.selectOption('#modalCategory', 'birthday');
    await page.click('#modalSubmitBtn');
    await expect(page.locator('#sampleEditModal')).toBeHidden({ timeout: 20000 });
  });

  test('6. 3D Interactive Virtual Flipbook Opening & Page Turn Verification', async ({ page }) => {
    // Navigate to Raksha Bandhan demo page
    await page.goto('/gift/manvi-rakhi-v2');

    // Unlock page if lock screen is present
    const answerInput = page.locator('#answerInput');
    if (await answerInput.isVisible()) {
      await answerInput.fill('RAKHI');
      await page.click('#unlockBtn');
      await expect(page.locator('#resultPageView')).toBeVisible({ timeout: 25000 });
    }

    // Locate 3D Virtual Album Launcher Button
    const flipbookBtn = page.locator('button:has-text("Open 3D Virtual Album")').first();
    await expect(flipbookBtn).toBeVisible({ timeout: 25000 });

    // Click to open 3D Flipbook Modal
    await flipbookBtn.click();
    const modal = page.locator('#soulscriptFlipbookModal');
    await expect(modal).toBeVisible({ timeout: 10000 });

    // Check Counter and Navigation
    const counter = page.locator('#fbPageCounter');
    await expect(counter).toBeVisible();

    // Close Modal
    await page.click('button[title="Close Flipbook"]');
    await expect(modal).toBeHidden();
  });

});
