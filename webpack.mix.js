let mix = require('laravel-mix');

   mix.setPublicPath('public');

   mix.copy('./node_modules/font-awesome/fonts', 'public/fonts/font-awsome');

   mix.js('resources/assets/js/app.js', './public/js/app.js')
   .sass('resources/assets/sass/app.scss', './public/css/pyangelo.css')
   .copy('./node_modules/skulpt/dist/skulpt.min.js', './public/js/')
   .copy('./node_modules/skulpt/dist/skulpt.min.js.map', './public/js/')
   .copy('./node_modules/skulpt/dist/skulpt-stdlib.js', './public/js/')
   .copy('./resources/assets/js/PyAngeloSetup.js', './public/js/')
   .copy('./resources/assets/js/editor.js', './public/js/')
   .copy('./resources/assets/js/playground.js', './public/js/')
   .copy('./resources/assets/js/dropzone.js', './public/js/')
   .copy('./resources/assets/js/notify.min.js', './public/js/')
   .copy('./resources/assets/js/lessonToggle.js', './public/js/')
   .copy('./resources/assets/js/lessonToggleTutorialPage.js', './public/js/')
   .copy('./resources/assets/js/lessonComments.js', './public/js/')
   .copy('./resources/assets/js/blogComments.js', './public/js/')
   .copy('./resources/assets/js/blogAlert.js', './public/js/')
   .copy('./resources/assets/js/questionComments.js', './public/js/')
   .copy('./resources/assets/js/questionAlert.js', './public/js/')
   .copy('./resources/assets/js/questionFavourite.js', './public/js/')
   .copy('./resources/assets/js/quiz.js', './public/js/')
   .copy('./resources/assets/js/notifications.js', './public/js/')
   .copy('./resources/assets/js/userSearch.js', './public/js/')
   .copy('./resources/assets/js/subscription.js', './public/js/')
   .copy('./resources/assets/js/new-payment-method.js', './public/js/')
   .version();
