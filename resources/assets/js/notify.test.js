describe('notify', () => {
  afterEach(() => {
    delete window.$
    delete window.jQuery
    document.body.innerHTML = ''
  })

  test('registers $.notify on the page jQuery', () => {
    jest.isolateModules(() => {
      const pageJquery = require('jquery')
      window.$ = window.jQuery = pageJquery
      require('./notify')
      expect(typeof pageJquery.notify).toBe('function')
    })
  })

  test('registers on whichever jQuery the page set, not a separately imported copy', () => {
    // An independent copy of jQuery, like the CDN jQuery 1.12 the sort pages
    // load alongside the bundled one.
    let cdnJquery
    jest.isolateModules(() => { cdnJquery = require('jquery') })
    window.$ = window.jQuery = cdnJquery

    jest.isolateModules(() => {
      const bundledJquery = require('jquery')
      require('./notify')
      expect(bundledJquery).not.toBe(cdnJquery)
      expect(typeof cdnJquery.notify).toBe('function')
      expect(bundledJquery.notify).toBeUndefined()
    })
  })

  test('$.notify shows a message on the page', () => {
    jest.isolateModules(() => {
      const pageJquery = require('jquery')
      window.$ = window.jQuery = pageJquery
      require('./notify')
      pageJquery.notify('Saved', { className: 'success', position: 'right-bottom' })
      expect(document.body.textContent).toContain('Saved')
    })
  })
})
