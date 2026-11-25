const { test, expect } = require('@playwright/test')

// Fail a page on any uncaught error, and on any failed request for a file
// from the Vite build. Under laravel-mix these scripts were classic scripts;
// under Vite they are ES modules in shared chunks, so load order and globals
// can break in ways only a real browser shows.
async function open (page, fixture) {
  const errors = []
  page.on('pageerror', error => errors.push(error.message))
  page.on('response', response => {
    if (response.url().includes('/build/') && response.status() >= 400) {
      errors.push(`${response.status()} ${response.url()}`)
    }
  })
  await page.goto(`/fixture/${fixture}`)
  await page.waitForLoadState('load')
  return errors
}

test('home page: styles, Bootstrap plugins and fonts load', async ({ page }) => {
  const errors = await open(page, 'home')
  await expect.poll(() => page.evaluate(() => window.jQuery && window.jQuery.fn.jquery)).toMatch(/^3\./)
  expect(await page.evaluate(() => typeof window.jQuery.fn.dropdown)).toBe('function')
  expect(await page.evaluate(() => getComputedStyle(document.querySelector('.navbar')).backgroundColor))
    .not.toBe('rgba(0, 0, 0, 0)')
  expect(await page.evaluate(async () => {
    await document.fonts.load('14px FontAwesome')
    return document.fonts.check('14px FontAwesome')
  })).toBe(true)
  expect(errors).toEqual([])
})

test('ask page: TinyMCE starts with the media plugin', async ({ page }) => {
  const errors = await open(page, 'ask')
  await expect(page.locator('.tox-tinymce')).toBeVisible()
  expect(await page.evaluate(() => Object.keys(window.tinymce.activeEditor.plugins).sort()))
    .toEqual(['code', 'image', 'link', 'lists', 'media'])
  expect(errors).toEqual([])
})

test('blog post: comment editor, alert and notify scripts run', async ({ page }) => {
  const errors = await open(page, 'blog-show')
  await expect(page.locator('.tox-tinymce')).toBeVisible()
  expect(await page.evaluate(() => typeof window.jQuery.notify)).toBe('function')
  await page.evaluate(() => window.jQuery.notify('Comment saved', { className: 'success' }))
  await expect(page.getByText('Comment saved')).toBeVisible()
  expect(errors).toEqual([])
})

test('lesson sort page keeps the CDN jQuery 1.12 with jQuery UI', async ({ page }) => {
  const errors = await open(page, 'lesson-sort')
  await expect.poll(() => page.evaluate(() => window.jQuery.fn.jquery)).toBe('1.12.4')
  expect(await page.evaluate(() => window.$ === window.jQuery)).toBe(true)
  expect(await page.evaluate(() => !!window.jQuery('#sortable').sortable('instance'))).toBe(true)
  expect(await page.evaluate(() => typeof window.jQuery.notify)).toBe('function')
  expect(await page.evaluate(() => typeof window.jQuery.fn.dropdown)).toBe('function')
  expect(errors).toEqual([])
})

test('playground: Skulpt runs Python and KEY_1 matches both 1 keys', async ({ page }) => {
  const errors = await open(page, 'playground')
  await expect(page.locator('.ace_editor')).toBeVisible()
  await expect(page.locator('link[rel=stylesheet][href*="pyangelo-notify"]')).toHaveCount(1)

  await page.evaluate(() => {
    window.ace.edit(document.querySelector('.ace_editor')).setValue([
      'print(KEY_1, KEY_0)',
      'setCanvasSize(200, 100)',
      'count = 0',
      'while count < 2:',
      '    if wasKeyPressed(KEY_1):',
      '        count += 1',
      '        print("1 pressed", count)',
      '    sleep(0.02)',
      'print("done")'
    ].join('\n'), -1)
  })
  await page.locator('#startStop').click()
  await expect(page.locator('#console')).toContainText('Digit1 Digit0')

  await page.locator('#canvas').click()
  for (const key of ['Digit1', 'Numpad1']) {
    await page.keyboard.down(key)
    await page.waitForTimeout(150)
    await page.keyboard.up(key)
    await page.waitForTimeout(150)
  }
  await expect(page.locator('#console')).toContainText('1 pressed 2')
  await expect(page.locator('#console')).toContainText('done')
  expect(errors).toEqual([])
})

test('subscription payment form: subscription.js asks the server for a payment intent', async ({ page }) => {
  // Answer the request the script makes on load, so no Stripe API call happens.
  await page.route('**/process-subscription', route => route.fulfill({
    contentType: 'application/json',
    body: JSON.stringify({ status: 'stripe-error', message: 'Fixture Stripe message' })
  }))
  const errors = await open(page, 'subscription-payment-form')
  await expect(page.locator('#stripe-error-message')).toHaveText('Fixture Stripe message')
  expect(errors).toEqual([])
})

test('payment method page: new-payment-method.js mounts the Stripe card field', async ({ page }) => {
  const errors = await open(page, 'payment-method')
  await expect(page.locator('#card-element iframe')).toHaveCount(1)
  expect(errors).toEqual([])
})

test('quiz: quiz.js asks a question and marks the right answer', async ({ page }) => {
  await page.route('**/quizzes/questions/7', route => route.fulfill({
    contentType: 'application/json',
    body: JSON.stringify({
      status: 'success',
      options: [{
        skill_question_id: 1,
        skill_question_type_id: 1,
        question: 'What does print(1 + 1) show?',
        question_image: null,
        hints: [],
        answers: [
          { skill_question_option_id: 10, option: '11', correct: 0 },
          { skill_question_option_id: 11, option: '2', correct: 1 }
        ]
      }]
    })
  }))
  const recorded = []
  await page.route('**/quizzes/questions/record**', route => {
    recorded.push(route.request().url())
    route.fulfill({ contentType: 'application/json', body: JSON.stringify({ status: 'success' }) })
  })
  const errors = await open(page, 'quiz')
  await page.locator('#startBtn').click()
  await expect(page.getByText('What does print(1 + 1) show?')).toBeVisible()
  await page.getByLabel('2', { exact: true }).check()
  await page.locator('#action').click()
  await expect(page.locator('#feedback')).toContainText('Well Done!')
  await expect.poll(() => recorded.length).toBeGreaterThan(0)
  expect(errors).toEqual([])
})
