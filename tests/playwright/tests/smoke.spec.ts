import { test, expect } from '@playwright/test';

/**
 * E2E smoke for hypeprototypervalidators.
 *
 * The plugin is a pure hook-handler library registering prototyper
 * validation rules in lib/hooks.php. It has no routes, actions, forms,
 * or admin surface of its own, so the only browser-visible signal that
 * activation did not break anything is that public pages still render
 * without PHP fatal markers. A second check on the login page exercises
 * a different view chain from the homepage and catches breakage in the
 * input-view extensions the plugin injects into.
 */
test.describe('hypeprototypervalidators', () => {
  test('homepage renders with no PHP fatal markers', async ({ page }) => {
    const response = await page.goto('/');
    expect(response).toBeTruthy();
    expect(response!.status()).toBeLessThan(500);
    const body = await page.content();
    expect(body).not.toContain('Fatal error');
    expect(body).not.toContain('Uncaught');
    expect(body).not.toContain('ParseError');
  });

  test('login page renders with no PHP fatal markers', async ({ page }) => {
    const response = await page.goto('/login');
    expect(response).toBeTruthy();
    expect(response!.status()).toBeLessThan(500);
    const body = await page.content();
    expect(body).not.toContain('Fatal error');
    expect(body).not.toContain('Uncaught');
    expect(body).not.toContain('ParseError');
  });
});
