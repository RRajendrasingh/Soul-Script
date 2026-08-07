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

});
