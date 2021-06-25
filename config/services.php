<?php
$di = new Framework\Di($GLOBALS);

$di->set('dotenv', function () use ($di) {
  return Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
});

$di->set('routes', function () use ($di) {
  return require('routes.php');
});

$di->set('router', function () use ($di) {
  $router = new AltoRouter();
  $router->addRoutes($di->get('routes'));
  return $router;
});

$di->set('request', function () use ($di) {
  return new Framework\Request($GLOBALS);
});

$di->set('response', function () use ($di) {
  return new Framework\Response('../views');
});

$di->set('auth', function () use ($di) {
  return new PyAngelo\Auth\Auth(
    $di->get('personRepository'),
    $di->get('request')
  );
});

/* Repositories start here */
$di->set('dbh', function () use ($di) {
  return new \Mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_DATABASE']);
});

$di->set('blogRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlBlogRepository($di->get('dbh'));
});

$di->set('campaignRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlCampaignRepository($di->get('dbh'));
});

$di->set('mailRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlMailRepository($di->get('dbh'));
});

$di->set('personRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlPersonRepository($di->get('dbh'));
});

$di->set('questionRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlQuestionRepository($di->get('dbh'));
});

$di->set('sketchRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlSketchRepository($di->get('dbh'));
});

$di->set('tutorialRepository', function () use ($di) {
  return new PyAngelo\Repositories\MysqlTutorialRepository($di->get('dbh'));
});

/* Form services start here */
$di->set('registerFormService', function () use ($di) {
  return new PyAngelo\FormServices\RegisterFormService (
    $di->get('personRepository'),
    $di->get('activateMembershipEmail'),
    $di->get('countryDetector'),
    [
      'requestScheme' => $di->get('request')->server['REQUEST_SCHEME'],
      'serverName' => $di->get('request')->server['SERVER_NAME']
    ]
  );
});

$di->set('forgotPasswordFormService', function () use ($di) {
  return new PyAngelo\FormServices\ForgotPasswordFormService (
    $di->get('personRepository'),
    $di->get('forgotPasswordEmail'),
    [
      'requestScheme' => $di->get('request')->server['REQUEST_SCHEME'],
      'serverName' => $di->get('request')->server['SERVER_NAME']
    ]
  );
});

$di->set('tutorialFormService', function () use ($di) {
  return new PyAngelo\FormServices\TutorialFormService (
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('lessonFormService', function () use ($di) {
  return new PyAngelo\FormServices\LessonFormService (
    $di->get('tutorialRepository')
  );
});

$di->set('blogFormService', function () use ($di) {
  return new PyAngelo\FormServices\BlogFormService (
    $di->get('auth'),
    $di->get('blogRepository')
  );
});


/* Email objects here */
$di->set('emailTemplate', function () use ($di) {
  return new PyAngelo\Email\EmailTemplate();
});

$di->set('activateMembershipEmail', function () use ($di) {
  return new PyAngelo\Email\ActivateMembershipEmail (
    $di->get('emailTemplate'),
    $di->get('mailRepository'),
    $di->get('mailer')
  );
});

$di->set('forgotPasswordEmail', function () use ($di) {
  return new PyAngelo\Email\ForgotPasswordEmail (
    $di->get('emailTemplate'),
    $di->get('mailRepository'),
    $di->get('mailer')
  );
});

$di->set('contactUsEmail', function () use ($di) {
  return new PyAngelo\Email\ContactUsEmail (
    $di->get('emailTemplate'),
    $di->get('mailRepository'),
    $di->get('mailer')
  );
});

$di->set('mailer', function () use ($di) {
  if ($_ENV['MAIL_METHOD'] == 'LoggerMail') {
    return new Framework\Mail\LoggerMail(
      $_ENV['APPLICATION_LOG_FILE']
    );
  }
  return new Framework\Mail\AwsSesMail(
    $_ENV['AWS_SES_KEY'],
    $_ENV['AWS_SES_SECRET'],
    $_ENV['AWS_SES_REGION']);
});

/* General objects start here */
$di->set('notificationAvatar', function () use ($di) {
  return new Framework\Presentation\Gravatar(25);
});

$di->set('avatar', function () use ($di) {
  return new Framework\Presentation\Gravatar(75);
});

$di->set('cloudFront', function () use ($di) {
  return new Framework\CloudFront\CloudFront(
    $_ENV['AWS_CLOUDFRONT_KEY'],
    $_ENV['AWS_CLOUDFRONT_SECRET'],
    $_ENV['AWS_CLOUDFRONT_REGION'],
    $_ENV['CLOUDFRONT_PRIVATE_KEY_FILE'],
    $_ENV['CLOUDFRONT_ACCESS_KEY_ID'],
    $_ENV['CLOUDFRONT_URL_HOST']
  );
});

$di->set('googleRecaptcha', function () use ($di) {
  return new \ReCaptcha\ReCaptcha(
    $_ENV['RECAPTCHA_SECRET'],
    new \ReCaptcha\RequestMethod\CurlPost()
  );
});

$di->set('recaptcha', function () use ($di) {
  return new Framework\Recaptcha\RecaptchaClient($di->get('googleRecaptcha'));
});

$di->set('countryDetector', function () use ($di) {
  return new PyAngelo\Utilities\CountryDetector(
    $di->get('request'),
    $di->get('geoReader')
  );
});

$di->set('geoReader', function () use ($di) {
  return new GeoIp2\Database\Reader($_ENV['GEOIP2_COUNTRY_DB']);
});

$di->set('HtmlPurifierPurify', function () use ($di) {
  $config = \HTMLPurifier_Config::createDefault();
  $config->set('HTML.Nofollow', true);
  $config->set('URI.Host', 'www.pyangelo.com');
  $config->set('HTML.SafeIframe', true);
  $config->set('URI.SafeIframeRegexp','%^//(www.youtube.com/embed/)%');
  $htmlPurifier = new \HTMLPurifier($config);
  return new Framework\Presentation\HtmlPurifierPurify($htmlPurifier);
});

$di->set('numberFormatter', function () use ($di) {
  return new \NumberFormatter('en_US', NumberFormatter::CURRENCY);
});

$di->set('sketchFiles', function () use ($di) {
  return new PyAngelo\Utilities\SketchFiles(
    $_ENV['APPLICATION_DIRECTORY']
  );
});

/* Controllers Start Here */
$di->set('PageNotFoundController', function () use ($di) {
  return new PyAngelo\Controllers\PageNotFoundController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('HomePageController', function () use ($di) {
  return new PyAngelo\Controllers\HomePageController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('AboutPageController', function () use ($di) {
  return new PyAngelo\Controllers\AboutPageController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('ContactPageController', function () use ($di) {
  return new PyAngelo\Controllers\ContactPageController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $_ENV['RECAPTCHA_KEY']
  );
});

$di->set('ContactValidateController', function () use ($di) {
  return new PyAngelo\Controllers\ContactValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('contactUsEmail'),
    $di->get('recaptcha')
  );
});

$di->set('ContactReceiptController', function () use ($di) {
  return new PyAngelo\Controllers\ContactReceiptController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('FaqPageController', function () use ($di) {
  return new PyAngelo\Controllers\FaqPageController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('PrivacyPolicyController', function () use ($di) {
  return new PyAngelo\Controllers\PrivacyPolicyController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('TermsController', function () use ($di) {
  return new PyAngelo\Controllers\TermsController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});


$di->set('RegisterController', function () use ($di) {
  return new PyAngelo\Controllers\Registration\RegisterController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $_ENV['RECAPTCHA_KEY']
  );
});

$di->set('RegisterValidateController', function () use ($di) {
  return new PyAngelo\Controllers\Registration\RegisterValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('registerFormService'),
    $di->get('recaptcha')
  );
});

$di->set('RegisterConfirmController', function () use ($di) {
  return new PyAngelo\Controllers\Registration\RegisterConfirmController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('RegisterActivateController', function () use ($di) {
  return new PyAngelo\Controllers\Registration\RegisterActivateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('RegisterThanksController', function () use ($di) {
  return new PyAngelo\Controllers\Registration\RegisterThanksController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('LogoutController', function () use ($di) {
  return new PyAngelo\Controllers\LogoutController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('LoginController', function () use ($di) {
  return new PyAngelo\Controllers\LoginController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $_ENV['RECAPTCHA_KEY']
  );
});

$di->set('LoginValidateController', function () use ($di) {
  return new PyAngelo\Controllers\LoginValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('recaptcha')
  );
});

$di->set('ForgotPasswordController', function () use ($di) {
  return new PyAngelo\Controllers\PasswordReset\ForgotPasswordController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('ForgotPasswordValidateController', function () use ($di) {
  return new PyAngelo\Controllers\PasswordReset\ForgotPasswordValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('forgotPasswordFormService')
  );
});

$di->set('ForgotPasswordConfirmController', function () use ($di) {
  return new PyAngelo\Controllers\PasswordReset\ForgotPasswordConfirmController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('ResetPasswordController', function () use ($di) {
  return new PyAngelo\Controllers\PasswordReset\ResetPasswordController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('ResetPasswordValidateController', function () use ($di) {
  return new PyAngelo\Controllers\PasswordReset\ResetPasswordValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('ProfileController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\ProfileController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository'),
    $di->get('avatar')
  );
});

$di->set('PasswordController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\PasswordController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('PasswordValidateController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\PasswordValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('NewsletterController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\NewsletterController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('NewsletterValidateController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\NewsletterValidateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository'),
    $di->get('campaignRepository')
  );
});

$di->set('FavouritesController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\FavouritesController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('SketchIndexController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchIndexController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});

$di->set('SketchCreateController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchCreateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository'),
    $di->get('sketchFiles')
  );
});

$di->set('SketchShowController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});

$di->set('SketchRunController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchRunController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});

$di->set('SketchPresentController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchPresentController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});

$di->set('SketchGetCodeController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchGetCodeController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository'),
    $_ENV['APPLICATION_DIRECTORY']
  );
});

$di->set('SketchSaveController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchSaveController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository'),
    $di->get('sketchFiles')
  );
});

$di->set('SketchForkController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchForkController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository'),
    $di->get('sketchFiles')
  );
});

$di->set('SketchRenameController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchRenameController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});

$di->set('SketchAddFileController', function () use ($di) {
  return new PyAngelo\Controllers\Sketch\SketchAddFileController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository'),
    $di->get('sketchFiles')
  );
});

$di->set('UploadAssetController', function () use ($di) {
  return new PyAngelo\Controllers\Upload\UploadAssetController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('sketchRepository')
  );
});
$di->set('CategoriesShowController', function () use ($di) {
  return new PyAngelo\Controllers\Categories\CategoriesShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('CategoriesSortController', function () use ($di) {
  return new PyAngelo\Controllers\Categories\CategoriesSortController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('CategoriesOrderController', function () use ($di) {
  return new PyAngelo\Controllers\Categories\CategoriesOrderController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('TutorialsIndexController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsIndexController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('TutorialsNewController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsNewController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('sketchRepository')
  );
});

$di->set('TutorialsCreateController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsCreateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialFormService')
  );
});

$di->set('TutorialsShowController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('TutorialsEditController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsEditController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('sketchRepository')
  );
});

$di->set('TutorialsUpdateController', function () use ($di) {
  return new PyAngelo\Controllers\Tutorials\TutorialsUpdateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialFormService')
  );
});

$di->set('LessonsNewController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsNewController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('sketchRepository'),
    $_ENV['CLONE_SKETCH_PERSON_ID']
  );
});

$di->set('LessonsCreateController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsCreateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('lessonFormService')
  );
});

$di->set('LessonsShowController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar'),
    $_ENV['SHOW_COMMENT_COUNT'],
    $di->get('sketchRepository'),
    $di->get('sketchFiles'),
    $_ENV['CLONE_SKETCH_PERSON_ID']
  );
});

$di->set('LessonsGetSignedUrlController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsGetSignedUrlController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('cloudFront')
  );
});

$di->set('LessonsEditController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsEditController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('sketchRepository')
  );
});

$di->set('LessonsUpdateController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsUpdateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('lessonFormService')
  );
});

$di->set('LessonsToggleCompletedController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsToggleCompletedController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsToggleFavouritedController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsToggleFavouritedController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsToggleAlertController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsToggleAlertController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsGetNextVideoController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsGetNextVideoController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsCommentController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsCommentController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar')
  );
});

$di->set('LessonsCommentUnpublishController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsCommentUnpublishController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('NotificationsController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\NotificationsController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('NotificationsReadController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\NotificationsReadController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('NotificationsAllReadController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\NotificationsAllReadController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('personRepository')
  );
});

$di->set('UnsubscribeThreadController', function () use ($di) {
  return new PyAngelo\Controllers\Profile\UnsubscribeThreadController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsSortController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsSortController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('LessonsOrderController', function () use ($di) {
  return new PyAngelo\Controllers\Lessons\LessonsOrderController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('tutorialRepository')
  );
});

$di->set('BlogIndexController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogIndexController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository'),
    $di->get('HtmlPurifierPurify')
  );
});

$di->set('BlogNewController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogNewController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository')
  );
});

$di->set('BlogCreateController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogCreateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogFormService')
  );
});

$di->set('BlogShowController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar'),
    $_ENV['SHOW_COMMENT_COUNT'],
  );
});

$di->set('BlogToggleAlertController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogToggleAlertController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository')
  );
});

$di->set('BlogCommentController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogCommentController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar')
  );
});

$di->set('BlogEditController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogEditController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository')
  );
});

$di->set('BlogUpdateController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogUpdateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogFormService')
  );
});

$di->set('BlogCommentUnpublishController', function () use ($di) {
  return new PyAngelo\Controllers\Blog\BlogCommentUnpublishController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('blogRepository')
  );
});

$di->set('ReferenceController', function () use ($di) {
  return new PyAngelo\Controllers\Reference\ReferenceController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('AssetLibraryController', function () use ($di) {
  return new PyAngelo\Controllers\Reference\AssetLibraryController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('AskTheTeacherIndexController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherIndexController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository'),
    $_ENV['QUESTIONS_PER_PAGE']
  );
});

$di->set('AskTheTeacherCategoryController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCategoryController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherAskController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherAskController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth')
  );
});

$di->set('AskTheTeacherCreateController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCreateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository'),
    $di->get('HtmlPurifierPurify')
  );
});

$di->set('AskTheTeacherQuestionListController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherQuestionListController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherDeleteController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherDeleteController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherThanksController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherThanksController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository'),
    $di->get('HtmlPurifierPurify')
  );
});

$di->set('AskTheTeacherEditController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherEditController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherUpdateController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherUpdateController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('notificationAvatar'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherShowController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherShowController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar'),
    $_ENV['SHOW_COMMENT_COUNT']
  );
});

$di->set('AskTheTeacherToggleAlertController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleAlertController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherToggleFavouriteController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherToggleFavouriteController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherCommentController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCommentController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository'),
    $di->get('HtmlPurifierPurify'),
    $di->get('avatar')
  );
});

$di->set('AskTheTeacherCommentUnpublishController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherCommentUnpublishController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherMyQuestionsController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherMyQuestionsController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});

$di->set('AskTheTeacherFavouriteQuestionsController', function () use ($di) {
  return new PyAngelo\Controllers\AskTheTeacher\AskTheTeacherFavouriteQuestionsController (
    $di->get('request'),
    $di->get('response'),
    $di->get('auth'),
    $di->get('questionRepository')
  );
});
