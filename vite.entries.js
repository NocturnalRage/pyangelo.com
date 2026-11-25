// Every file Vite builds as its own entry point. Templates load them with
// vite('<path>'); vite-entries.test.js checks the two lists agree.
export const styleEntries = [
  'resources/assets/sass/app.scss'
]

export const scriptEntries = [
  'resources/assets/js/app.js',
  'resources/assets/js/backToTop.js',
  'resources/assets/js/blogAlert.js',
  'resources/assets/js/blogComments.js',
  'resources/assets/js/collection.js',
  'resources/assets/js/editor.js',
  'resources/assets/js/lessonComments.js',
  'resources/assets/js/lessonToggle.js',
  'resources/assets/js/lessonToggleTutorialPage.js',
  'resources/assets/js/new-payment-method.js',
  'resources/assets/js/notifications.js',
  'resources/assets/js/notify.js',
  'resources/assets/js/playground.js',
  'resources/assets/js/questionAlert.js',
  'resources/assets/js/questionComments.js',
  'resources/assets/js/questionFavourite.js',
  'resources/assets/js/quiz.js',
  'resources/assets/js/SkulptSketch.js',
  'resources/assets/js/SkulptSketchCanvasOnly.js',
  'resources/assets/js/subscription.js',
  'resources/assets/js/tinymce-comments.js',
  'resources/assets/js/tinymce-default-config.js',
  'resources/assets/js/userSearch.js'
]

export const allEntries = [...styleEntries, ...scriptEntries]
