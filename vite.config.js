import { defineConfig } from 'vite'
import { resolve } from 'path'
import { allEntries } from './vite.entries.js'

const rootDir = __dirname

export default defineConfig({
  base: '/build/',
  publicDir: false,
  resolve: {
    alias: [
      // Font paths in the Sass use ~package/... for node_modules, as they did
      // under laravel-mix, so Vite bundles the font files.
      { find: /^~(?=[^/])/, replacement: resolve(rootDir, 'node_modules') + '/' },
      // The Ace editor is loaded from a CDN script tag, not bundled.
      { find: /^ace$/, replacement: resolve(rootDir, 'resources/assets/js/aceGlobal.js') }
    ]
  },
  css: {
    preprocessorOptions: {
      scss: {
        loadPaths: [resolve(rootDir, 'node_modules')],
        quietDeps: true,
        silenceDeprecations: ['import', 'global-builtin', 'color-functions']
      }
    }
  },
  build: {
    manifest: true,
    outDir: 'public/build',
    emptyOutDir: true,
    // Skulpt and TinyMCE are large single bundles.
    chunkSizeWarningLimit: 2000,
    rollupOptions: {
      output: {
        // Run modules in import order even when they land in shared chunks.
        // TinyMCE's skin files are side-effect imports that call the global
        // tinymce, so they must run after tinymce/tinymce sets it.
        strictExecutionOrder: true
      },
      input: Object.fromEntries(
        allEntries.map(entry => [entry.replace(/^resources\/assets\//, ''), resolve(rootDir, entry)])
      )
    }
  }
})
