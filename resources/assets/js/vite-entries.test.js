/**
 * @jest-environment node
 */
import fs from 'fs'
import path from 'path'
import { allEntries, scriptEntries, styleEntries } from '../../../vite.entries.js'

const root = path.resolve(__dirname, '../../..')

function phpFiles (dir) {
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap(entry => {
    const full = path.join(dir, entry.name)
    if (entry.isDirectory()) return phpFiles(full)
    return entry.name.endsWith('.php') ? [full] : []
  })
}

const templates = phpFiles(path.join(root, 'views')).map(file => ({
  file: path.relative(root, file),
  source: fs.readFileSync(file, 'utf8')
}))

function calls (helper) {
  const pattern = new RegExp(`\\b${helper}\\('([^']+)'\\)`, 'g')
  return templates.flatMap(({ file, source }) =>
    [...source.matchAll(pattern)].map(match => ({ file, entry: match[1] }))
  )
}

describe('Vite entry points', () => {
  test('every entry file exists', () => {
    const missing = allEntries.filter(entry => !fs.existsSync(path.join(root, entry)))
    expect(missing).toEqual([])
  })

  test('entries are listed once', () => {
    expect(new Set(allEntries).size).toBe(allEntries.length)
  })

  test('every viteScript() call in a template names a script entry', () => {
    const unknown = calls('viteScript').filter(({ entry }) => !scriptEntries.includes(entry))
    expect(unknown).toEqual([])
  })

  test('every vite() call in a template names a stylesheet entry', () => {
    const unknown = calls('vite').filter(({ entry }) => !styleEntries.includes(entry))
    expect(unknown).toEqual([])
  })

  test('every entry is used by a template', () => {
    const used = new Set([...calls('viteScript'), ...calls('vite')].map(({ entry }) => entry))
    expect(allEntries.filter(entry => !used.has(entry))).toEqual([])
  })

  test('no template still uses laravel-mix', () => {
    const leftovers = templates.filter(({ source }) => /\bmix\(/.test(source)).map(({ file }) => file)
    expect(leftovers).toEqual([])
  })

  test('scripts are loaded with viteScript(), not a plain script tag', () => {
    // A plain <script src> tag would load the module as a classic script and
    // skip the CSS and preload links viteScript() adds.
    const plain = templates.filter(({ source }) => /<script[^>]*src="<\?= vite\(/.test(source))
      .map(({ file }) => file)
    expect(plain).toEqual([])
  })
})
