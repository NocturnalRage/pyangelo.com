<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Email\ContactUsEmail;
use PyAngelo\Controllers\Controller;
use Framework\Recaptcha\RecaptchaClient;
use Framework\Turnstile\TurnstileVerifier;
use Framework\{Request, Response};

class ContactValidateController extends Controller {
  protected $contactUsEmail;
  protected $turnstileVerifier;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ContactUsEmail $contactUsEmail,
    TurnstileVerifier $turnstileVerifier
  ) {
    parent::__construct($request, $response, $auth);
    $this->contactUsEmail = $contactUsEmail;
    $this->turnstileVerifier = $turnstileVerifier;
  }

  public function exec() {
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please contact us from the PyAngelo website!', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }

    if (empty($this->request->post['cf-turnstile-response'])) {
      $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }

    $token = $this->request->post['cf-turnstile-response'];
    $ip = $this->request->server['REMOTE_ADDR'];
    $result = $this->turnstileVerifier->verify($token, $ip);

    $isOk = $result['ok'] ?? false;
    if (!$isOk) {
      $this->logMessage('Turnstile failed: ' . json_encode([
        'errors' => $result['errors'] ?? [],
        'host'   => $result['host'] ?? null,
      ]), 'INFO');
      $this->flash('Cloudflare turnstile could not verify you were a human. Please try again.', 'warning');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }

    if (!$this->formDataIsValid($this->request->post)) {
      $this->flash('There were some errors. Please fix these and resubmit your inquiry.', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }
    $this->sendContactUsEmail($this->request->post);

    $this->response->header('Location: /contact-receipt');
    return $this->response;
  }

  private function formDataIsValid($formData) {
    // Validate Name
    if (empty($formData['name'])) {
      $_SESSION['errors']['name'] = "Please enter your name.";
    }
    elseif (strlen($formData['name']) > 100) {
      $_SESSION['errors']['name'] = "Your name can be no longer than 100 characters.";
    }
    // Validate Email
    if (empty($formData['email'])) {
      $_SESSION['errors']['email'] = "You must supply an email address.";
    }
    elseif (strlen($formData['email']) > 100) {
      $_SESSION['errors']['email'] = "The email address can be no longer than 100 characters.";
    }
    elseif (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
      $_SESSION['errors']['email'] = "The email address is not valid.";
    }
    // Validate Inquiry
    if (empty($formData['inquiry'])) {
      $_SESSION['errors']['inquiry'] = "Please enter your inquiry.";
    }
    return empty($_SESSION["errors"]);
  }

  private function sendContactUsEmail($formData) {
    $mailInfo = [
      'name' => $formData['name'],
      'replyEmail' => $formData['email'],
      'inquiry' => $formData['inquiry']
    ];
    $this->contactUsEmail->sendEmail($mailInfo);
  }
}
