import { expect, test } from '@playwright/test';

test('Testing features of logged in user...', async ({ page }) => {
	await page.goto('/');
});
