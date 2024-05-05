import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver'
import 'tinymce/models/dom'
import 'tinymce/skins/ui/oxide/skin.js'
import 'tinymce/icons/default'
import 'tinymce/plugins/link'
import 'tinymce/plugins/image'
import 'tinymce/plugins/lists'

/* content UI CSS is required */
import 'tinymce/skins/ui/oxide/content.js'
/* The default content CSS can be changed or replaced with appropriate CSS for the editor content. */
import 'tinymce/skins/content/default/content.js'

tinymce.init({
  license_key: 'gpl',
  selector: 'textarea.tinymce',
  toolbar_items_size: 'small',
  plugins: 'link, image, lists',
  relative_urls: false,
  browser_spellcheck: true,
  toolbar: 'undo redo | formats formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media | hr blockquote',
  image_class_list: [{ title: 'Responsive Image', value: 'img-responsive' }],
  image_caption: true,
  menubar: false,
  statusbar: false,
  skin_url: 'default',
  content_css: 'default'
})
