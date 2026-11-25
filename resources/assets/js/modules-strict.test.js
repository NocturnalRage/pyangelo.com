/**
 * @jest-environment node
 */
import fs from 'fs'
import path from 'path'
import { ESLint } from 'eslint'

// Vite loads every script as an ES module, and modules run in strict mode:
// assigning to a variable that was never declared throws a ReferenceError
// instead of creating a global, as it did when laravel-mix copied some of these
// files as plain scripts. ESLint's no-undef rule finds those assignments.
//
// Globals the pages really provide, from CDN or inline script tags:
const pageGlobals = {
  $: 'readonly', // jQuery, set by jqueryGlobal.js or a CDN script
  jQuery: 'readonly',
  tinymce: 'readonly', // set by the TinyMCE bundle
  tinyMCE: 'readonly',
  videojs: 'readonly', // video.js CDN script on lesson pages
  YT: 'readonly', // YouTube iframe API, loaded by lessonToggle.js
  Stripe: 'readonly', // js.stripe.com script on payment pages
  stripe: 'readonly', // inline script on the payment method page
  elements: 'readonly', // inline script on the payment method page
  google: 'readonly' // Google Charts on admin report pages
}

const dir = __dirname
const sources = fs.readdirSync(dir)
  .filter(name => name.endsWith('.js') && !name.endsWith('.test.js') && name !== 'notify.min.js')

test('there are scripts to check', () => {
  expect(sources.length).toBeGreaterThan(20)
})

test('no script uses an undeclared variable', async () => {
  const eslint = new ESLint({
    cwd: dir,
    useEslintrc: false,
    overrideConfig: {
      env: { browser: true, es2021: true },
      parserOptions: { ecmaVersion: 2022, sourceType: 'module' },
      globals: pageGlobals,
      rules: { 'no-undef': 'error' }
    }
  })
  const results = await eslint.lintFiles(sources.map(name => path.join(dir, name)))
  const problems = results.flatMap(result => result.messages.map(message =>
    `${path.basename(result.filePath)}:${message.line} ${message.message}`
  ))
  expect(problems).toEqual([])
})
