describe('jqueryGlobal', () => {
  afterEach(() => {
    delete window.$
    delete window.jQuery
  })

  test('sets the bundled jQuery as $ and jQuery when the page has none', () => {
    jest.isolateModules(() => {
      const bundled = require('jquery')
      const exported = require('./jqueryGlobal').default
      expect(window.jQuery).toBe(bundled)
      expect(window.$).toBe(bundled)
      expect(exported).toBe(bundled)
    })
  })

  test('keeps a jQuery the page already loaded, such as the CDN copy on the sort pages', () => {
    const cdnJquery = function () {}
    cdnJquery.fn = { sortable () {} }
    window.$ = window.jQuery = cdnJquery
    jest.isolateModules(() => {
      const exported = require('./jqueryGlobal').default
      expect(window.jQuery).toBe(cdnJquery)
      expect(window.$).toBe(cdnJquery)
      expect(exported).toBe(cdnJquery)
    })
  })
})
