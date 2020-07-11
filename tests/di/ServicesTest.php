<?php
namespace Tests\di;

use PHPUnit\Framework\TestCase;
use Framework\Response;

class ServicesTest extends TestCase {

  public function setUp(): void {
    require(dirname(__FILE__) . '/../../vendor/autoload.php');
    require(dirname(__FILE__) . '/../../config/services.php');
    $this->di = $di;

    # Load environment variables using dotenv
    $dotenv = $di->get('dotenv', '.env.test');
    $dotenv->load();
  }

  public function testDotenv() {
    $dotenv = $this->di->get('dotenv', '.env.test');
    $this->assertSame(get_class($dotenv), 'Dotenv\Dotenv');
  }
  public function testRouter() {
    $router = $this->di->get('router');
    $this->assertSame(get_class($router), 'AltoRouter');
  }
  public function testRequest() {
    $request = $this->di->get('request');
    $this->assertSame(get_class($request), 'Framework\Request');
  }
  public function testResponse() {
    $response = $this->di->get('response');
    $this->assertSame(get_class($response), 'Framework\Response');
  }
  public function testAuth() {
    $auth = $this->di->get('auth');
    $this->assertSame(get_class($auth), 'PyAngelo\Auth\Auth');
  }
  public function testDbh() {
    $dbh = $this->di->get('dbh');
    $this->assertSame(get_class($dbh), 'mysqli');
  }
  public function testBlogRepository() {
    $repository = $this->di->get('blogRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlBlogRepository');
  }
  public function testCampaignRepository() {
    $repository = $this->di->get('campaignRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlCampaignRepository');
  }
  public function testMailRepository() {
    $repository = $this->di->get('mailRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlMailRepository');
  }
  public function testPersonRepository() {
    $repository = $this->di->get('personRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlPersonRepository');
  }
  public function testSketchRepository() {
    $repository = $this->di->get('sketchRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlSketchRepository');
  }
  public function testTutorialRepository() {
    $repository = $this->di->get('tutorialRepository');
    $this->assertSame(get_class($repository), 'PyAngelo\Repositories\MysqlTutorialRepository');
  }
  public function testRegisterFormService() {
    $request = $this->di->get('request');
    $request->server['REQUEST_SCHEME'] = 'https';
    $request->server['SERVER_NAME'] = 'www.pyangelo.com';
    $registerFormService = $this->di->get('registerFormService');
    $this->assertSame(get_class($registerFormService), 'PyAngelo\FormServices\RegisterFormService');
  }
  public function testForgotPasswordFormService() {
    $request = $this->di->get('request');
    $request->server['REQUEST_SCHEME'] = 'https';
    $request->server['SERVER_NAME'] = 'www.pyangelo.com';
    $forgotPasswordFormService = $this->di->get('forgotPasswordFormService');
    $this->assertSame(get_class($forgotPasswordFormService), 'PyAngelo\FormServices\ForgotPasswordFormService');
  }
  public function testTutorialFormService() {
    $numberFormatter = $this->di->get('tutorialFormService');
    $this->assertSame(get_class($numberFormatter), 'PyAngelo\FormServices\TutorialFormService');
  }
  public function testLessonFormService() {
    $numberFormatter = $this->di->get('lessonFormService');
    $this->assertSame(get_class($numberFormatter), 'PyAngelo\FormServices\LessonFormService');
  }
  public function testEmailTemplate() {
    $numberFormatter = $this->di->get('emailTemplate');
    $this->assertSame(get_class($numberFormatter), 'PyAngelo\Email\EmailTemplate');
  }
  public function testAvatar() {
    $avatar = $this->di->get('avatar');
    $this->assertSame(get_class($avatar), 'Framework\Presentation\Gravatar');
  }
  public function testCloudFront() {
    $cloudFront = $this->di->get('cloudFront');
    $this->assertSame(get_class($cloudFront), 'Framework\CloudFront\CloudFront');
  }
  public function testActivateMembershipEmail() {
    $email = $this->di->get('activateMembershipEmail');
    $this->assertSame(get_class($email), 'PyAngelo\Email\ActivateMembershipEmail');
  }
  public function testForgotPasswordEmail() {
    $email = $this->di->get('forgotPasswordEmail');
    $this->assertSame(get_class($email), 'PyAngelo\Email\ForgotPasswordEmail');
  }
  public function testMailer() {
    $mailer = $this->di->get('mailer');
    $this->assertSame(get_class($mailer), 'Framework\Mail\LoggerMail');
  }
  public function testCountryDetector() {
    $countryDetector = $this->di->get('countryDetector');
    $this->assertSame(get_class($countryDetector), 'PyAngelo\Utilities\CountryDetector');
  }
  public function testGeoReader() {
    $geoReader = $this->di->get('geoReader');
    $this->assertSame(get_class($geoReader), 'GeoIp2\Database\Reader');
  }
  public function testNumberFormatter() {
    $numberFormatter = $this->di->get('numberFormatter');
    $this->assertSame(get_class($numberFormatter), 'NumberFormatter');
  }
  public function testSketchFiles() {
    $sketchFiles = $this->di->get('sketchFiles');
    $this->assertInstanceOf('PyAngelo\Utilities\SketchFiles', $sketchFiles);
  }
  public function testPageNotFoundController() {
    $controller = $this->di->newInstance('PageNotFoundController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PageNotFoundController');
  }
  public function testHomePageController() {
    $controller = $this->di->newInstance('HomePageController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\HomePageController');
  }
  public function testRegisterController() {
    $controller = $this->di->newInstance('RegisterController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Registration\RegisterController');
  }
  public function testRegisterValidateController() {
    $request = $this->di->get('request');
    $request->server['REQUEST_SCHEME'] = 'https';
    $request->server['SERVER_NAME'] = 'www.pyangelo.com';
    $controller = $this->di->newInstance('RegisterValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Registration\RegisterValidateController');
  }
  public function testRegisterConfirmController() {
    $controller = $this->di->newInstance('RegisterConfirmController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Registration\RegisterConfirmController');
  }
  public function testRegisterActivateController() {
    $controller = $this->di->newInstance('RegisterActivateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Registration\RegisterActivateController');
  }
  public function testRegisterThanksController() {
    $controller = $this->di->newInstance('RegisterThanksController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Registration\RegisterThanksController');
  }
  public function testLogoutController() {
    $controller = $this->di->newInstance('LogoutController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\LogoutController');
  }
  public function testLoginController() {
    $controller = $this->di->newInstance('LoginController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\LoginController');
  }
  public function testLoginValidateController() {
    $controller = $this->di->newInstance('LoginValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\LoginValidateController');
  }
  public function testForgotPasswordController() {
    $controller = $this->di->newInstance('ForgotPasswordController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordController');
  }
  public function testForgotPasswordValidateController() {
    $request = $this->di->get('request');
    $request->server['REQUEST_SCHEME'] = 'https';
    $request->server['SERVER_NAME'] = 'www.pyangelo.com';
    $controller = $this->di->newInstance('ForgotPasswordValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordValidateController');
  }
  public function testForgotPasswordConfirmController() {
    $controller = $this->di->newInstance('ForgotPasswordConfirmController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PasswordReset\ForgotPasswordConfirmController');
  }
  public function testResetPasswordController() {
    $controller = $this->di->newInstance('ResetPasswordController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PasswordReset\ResetPasswordController');
  }
  public function testResetPasswordValidateController() {
    $controller = $this->di->newInstance('ResetPasswordValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\PasswordReset\ResetPasswordValidateController');
  }
  public function testProfileController() {
    $controller = $this->di->newInstance('ProfileController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\ProfileController');
  }
  public function testPasswordController() {
    $controller = $this->di->newInstance('PasswordController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\PasswordController');
  }
  public function testPasswordValidateController() {
    $controller = $this->di->newInstance('PasswordValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\PasswordValidateController');
  }
  public function testNewsletterController() {
    $controller = $this->di->newInstance('NewsletterController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\NewsletterController');
  }
  public function testNewsletterValidateController() {
    $controller = $this->di->newInstance('NewsletterValidateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\NewsletterValidateController');
  }
  public function testSketchIndexController() {
    $controller = $this->di->newInstance('SketchIndexController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchIndexController');
  }
  public function testSketchCreateController() {
    $controller = $this->di->newInstance('SketchCreateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchCreateController');
  }
  public function testSketchShowController() {
    $controller = $this->di->newInstance('SketchShowController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchShowController');
  }
  public function testSketchRunController() {
    $controller = $this->di->newInstance('SketchRunController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchRunController');
  }
  public function testSketchPresentController() {
    $controller = $this->di->newInstance('SketchPresentController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchPresentController');
  }
  public function testSketchGetCodeController() {
    $controller = $this->di->newInstance('SketchGetCodeController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchGetCodeController');
  }
  public function testSketchSaveController() {
    $controller = $this->di->newInstance('SketchSaveController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchSaveController');
  }
  public function testSketchForkController() {
    $controller = $this->di->newInstance('SketchForkController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchForkController');
  }
  public function testSketchRenameController() {
    $controller = $this->di->newInstance('SketchRenameController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchRenameController');
  }
  public function testSketchAddFileController() {
    $controller = $this->di->newInstance('SketchAddFileController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Sketch\SketchAddFileController');
  }
  public function testUploadAssetController() {
    $controller = $this->di->newInstance('UploadAssetController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Upload\UploadAssetController');
  }
  public function testCategoriesShowController() {
    $controller = $this->di->newInstance('CategoriesShowController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Categories\CategoriesShowController');
  }
  public function testCategoriesSortController() {
    $controller = $this->di->newInstance('CategoriesSortController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Categories\CategoriesSortController');
  }
  public function testCategoriesOrderController() {
    $controller = $this->di->newInstance('CategoriesOrderController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Categories\CategoriesOrderController');
  }
  public function testTutorialsIndexController() {
    $controller = $this->di->newInstance('TutorialsIndexController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsIndexController');
  }
  public function testTutorialsNewController() {
    $controller = $this->di->newInstance('TutorialsNewController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsNewController');
  }
  public function testTutorialsCreateController() {
    $controller = $this->di->newInstance('TutorialsCreateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsCreateController');
  }
  public function testTutorialsShowController() {
    $controller = $this->di->newInstance('TutorialsShowController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsShowController');
  }
  public function testTutorialEditController() {
    $controller = $this->di->newInstance('TutorialsEditController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsEditController');
  }
  public function testTutorialsUpdateController() {
    $controller = $this->di->newInstance('TutorialsUpdateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Tutorials\TutorialsUpdateController');
  }
  public function testLessonsNewController() {
    $controller = $this->di->newInstance('LessonsNewController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsNewController');
  }
  public function testLessonsCreateController() {
    $controller = $this->di->newInstance('LessonsCreateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsCreateController');
  }
  public function testLessonsShowController() {
    $controller = $this->di->newInstance('LessonsShowController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsShowController');
  }
  public function testLessonsGetSignedUrlController() {
    $controller = $this->di->newInstance('LessonsGetSignedUrlController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsGetSignedUrlController');
  }
  public function testLessonsEditController() {
    $controller = $this->di->newInstance('LessonsEditController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsEditController');
  }
  public function testLessonsUpdateController() {
    $controller = $this->di->newInstance('LessonsUpdateController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsUpdateController');
  }
  public function testLessonsToggleCompletedController() {
    $controller = $this->di->newInstance('LessonsToggleCompletedController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsToggleCompletedController');
  }
  public function testLessonsToggleFavouritedController() {
    $controller = $this->di->newInstance('LessonsToggleFavouritedController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsToggleFavouritedController');
  }
  public function testLessonsToggleAlertController() {
    $controller = $this->di->newInstance('LessonsToggleAlertController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsToggleAlertController');
  }
  public function testLessonsGetNextVideoController() {
    $controller = $this->di->newInstance('LessonsGetNextVideoController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsGetNextVideoController');
  }
  public function testLessonsCommentController() {
    $controller = $this->di->newInstance('LessonsCommentController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Lessons\LessonsCommentController');
  }
  public function testNotificationsController() {
    $controller = $this->di->newInstance('NotificationsController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\NotificationsController');
  }
  public function testNotificationsReadController() {
    $controller = $this->di->newInstance('NotificationsReadController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\NotificationsReadController');
  }
  public function testNotificationsAllReadController() {
    $controller = $this->di->newInstance('NotificationsAllReadController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\NotificationsAllReadController');
  }
  public function testUnsubscribeThreadController() {
    $controller = $this->di->newInstance('UnsubscribeThreadController');
    $this->assertSame(get_class($controller), 'PyAngelo\Controllers\Profile\UnsubscribeThreadController');
  }
}
?>
