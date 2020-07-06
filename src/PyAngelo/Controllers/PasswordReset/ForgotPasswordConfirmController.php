<?php
namespace PyAngelo\Controllers\PasswordReset;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class ForgotPasswordConfirmController extends Controller {
  public function exec() {
    if ($this->auth->loggedIn())
      return $this->redirectToPasswordPage();

    if (empty($this->request->get['email']))
      return $this->redirectToForgotPasswordPageWithErrorMessage();

    $this->response->setView('password-reset/forgot-password-confirm.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Request Link Sent',
      'metaDescription' => "If we have the email you entered in our system then a message has been sent with a password reset link.",
      'activeLink' => 'Home',
      'email' => $this->request->get['email'],
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    return $this->response;
  }

  private function redirectToPasswordPage() {
    $this->flash('You are already logged in so you can simply update your password.', 'danger');
    $this->response->header('Location: /password');
    return $this->response;
  }

  private function redirectToForgotPasswordPageWithErrorMessage() {
    $this->flash('Sorry, something went wrong, please enter your email address again and we\'ll send you instructions on how to reset your password.', 'danger');
    $this->response->header('Location: /forgot-password');
    return $this->response;
  }
}
