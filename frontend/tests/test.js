import { expect, test } from '@playwright/test';

test('Testing language switch...', async ({ page }) => {
	await page.goto('/en');
	await expect(page).toHaveTitle(/Home/);

	await page.selectOption('select[name="lang"]', 'de');
	await expect(page).toHaveURL(/.*de/);
});

test('Testing features of logged in user...', async ({ page }) => {
	await page.goto('/en/account');
	await expect(page).toHaveURL(/.*en\/home/);

	await page.click('button[name="login-btn"]');

	await page.goto('/en/account');
	await expect(page).toHaveURL(/.*en\/account\/*/);
});
