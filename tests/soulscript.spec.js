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

    // Verify Buyer Portal Login Header & Input Form
    const portalHeader = page.locator('h2:has-text("Buyer Portal Login")').first();
    await expect(portalHeader).toBeVisible({ timeout: 10000 });

    const loginEmail = page.locator('#loginEmail');
    await expect(loginEmail).toBeVisible();

    const loginPassword = page.locator('#loginPassword');
    await expect(loginPassword).toBeVisible();

    const loginBtn = page.locator('#loginBtn');
    await expect(loginBtn).toBeVisible();
  });

  test('4. Live Demo Gift Page Lock Screen & Single-Page Layout', async ({ page }) => {
    await page.goto('/gift/ananya-rohan');

    // Verify Lock Screen View
    const lockView = page.locator('#lockScreenView');
    await expect(lockView).toBeVisible({ timeout: 15000 });

    const hintQuestion = page.locator('#lockHintQuestion');
    await expect(hintQuestion).toBeVisible();
  });

});
