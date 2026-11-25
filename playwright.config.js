const { defineConfig } = require('@playwright/test')

// Smoke tests that load the real Vite build in Chromium. Run with
// `npm run test:e2e`, which builds first. Pages are real templates rendered
// with fixture data by tests/e2e/router.php, so no database is needed.
// The sort and playground pages load jQuery 1.12 and Ace from CDNs, so those
// tests need internet access.
module.exports = defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  workers: 1,
  timeout: 60000,
  expect: { timeout: 10000 },
  use: {
    baseURL: 'http://127.0.0.1:4180',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure'
  },
  webServer: {
    command: 'php -S 127.0.0.1:4180 -t public tests/e2e/router.php',
    url: 'http://127.0.0.1:4180/fixture/home',
    reuseExistingServer: false,
    timeout: 30000,
    // PHP's server logs every request to stderr.
    stderr: 'ignore'
  }
})
