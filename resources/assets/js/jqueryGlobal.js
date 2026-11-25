import jquery from 'jquery'

// Bootstrap 3's plugins and the page scripts use the global jQuery. Keep one
// the page already loaded: the lesson and category sort pages load jQuery 1.12
// and jQuery UI from a CDN, and their sortable lists need that copy. Module
// scripts run after classic ones, so replacing it here would break them.
if (!window.jQuery) {
  window.$ = window.jQuery = jquery
}

export default window.jQuery
