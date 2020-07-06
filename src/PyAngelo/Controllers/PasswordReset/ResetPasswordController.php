<?php
namespace PyAngelo\Controllers\PasswordReset;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ResetPasswordController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn()) 
      return $this->redirectToHomePage();

    if (! $this->tokenIsValid())
      return $this->redirectToForgotPasswordPage();

    $this->response->setView('password-reset/reset-password.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Reset Password | PyAngelo',
      'metaDescription' => "Enter your new password.",
      'activeLink' => 'Home',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'token' => $this->request->get['token']
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  private function tokenIsValid() {
    if (empty($this->request->get['token']))
      return false;
    return $this->auth->isPasswordResetTokenValid($this->request->get['token']);
  }

  private function redirectToHomePage() {
    $this->flash('You are already logged in!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToForgotPasswordPage() {
    $this->flash('We could not reset your password. Please start the process again.', 'danger');
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }
}
?>
