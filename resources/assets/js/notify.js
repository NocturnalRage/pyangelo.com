// notify.min.js is a UMD jQuery plugin. Bundled as a module it exports a
// registration function instead of attaching itself to the global jQuery, so
// register it on the page's jQuery here. That may be the CDN jQuery 1.12 the
// sort pages load, which is why this uses window.jQuery, not an import.
import registerNotify from './notify.min.js'

registerNotify(window, window.jQuery)
