import tinymce from 'tinymce/tinymce'
import 'tinymce/themes/silver'
import 'tinymce/models/dom'
import 'tinymce/skins/ui/oxide/skin.js'
import 'tinymce/icons/default'
import 'tinymce/plugins/link'
import 'tinymce/plugins/image'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/code'
import 'tinymce/plugins/media'

/* content UI CSS is required */
import 'tinymce/skins/ui/oxide/content.js'
/* The default content CSS can be changed or replaced with appropriate CSS for the editor content. */
import 'tinymce/skins/content/default/content.js'

tinymce.init({
  license_key: 'gpl',
  selector: 'textarea.tinymce',
  toolbar_items_size: 'small',
  plugins: 'link, image, lists, code, media',
  relative_urls: false,
  browser_spellcheck: true,
  toolbar: 'undo redo | formats formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image media | hr blockquote | code',
  image_class_list: [
    { title: 'Responsive Image', value: 'img-responsive' },
    { title: 'Center Image', value: 'center-block' },
    { title: 'Pull Left', value: 'pull-left' },
    { title: 'Pull Right', value: 'pull-right' }
  ],
  image_dimensions: false,
  menubar: false,
  statusbar: false,
  skin_url: 'default',
  content_css: 'default'
})
