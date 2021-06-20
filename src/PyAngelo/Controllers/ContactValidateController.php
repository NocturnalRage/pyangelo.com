<?php
namespace PyAngelo\Controllers;

use PyAngelo\Auth\Auth;
use PyAngelo\Email\ContactUsEmail;
use PyAngelo\Controllers\Controller;
use Framework\Recaptcha\RecaptchaClient;
use Framework\{Request, Response};

class ContactValidateController extends Controller {
  protected $contactUsEmail;
  protected $recaptcha;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    ContactUsEmail $contactUsEmail,
    RecaptchaClient $recaptcha
  ) {
    parent::__construct($request, $response, $auth);
    $this->contactUsEmail = $contactUsEmail;
    $this->recaptcha = $recaptcha;
  }

  public function exec() {
    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please contact us from the PyAngelo website!', 'danger');
      $this->request->session['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }

    if (empty($this->request->post['g-recaptcha-response'])) {
      $this->flash('Recaptcha could not verify you were a human. Please try again.', 'warning');
      $this->request->session['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }
    $expectedRecaptchaAction = "contactuswithversion3";
    if (!$this->recaptcha->verified(
      $this->request->server['SERVER_NAME'],
      $expectedRecaptchaAction,
      $this->request->post['g-recaptcha-response'],
      $this->request->server['REMOTE_ADDR']
    )) {
      $this->flash('Recaptcha could not verify you were a human. Please try again.', 'warning');
      $this->request->session['formVars'] = $this->request->post;
      $this->response->header('Location: /contact');
      return $this->response;
    }

    if (!$this->formDataIsValid($this->request->post)) {
      $this->flash('There were some errors. Please fix these and resubmit your inquiry.', 'danger');
      $this->request->session['formVars'] = $this->request->post;
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
      $this->request->session['errors']['name'] = "Please enter your name.";
    }
    elseif (strlen($formData['name']) > 100) {
      $this->request->session['errors']['name'] = "Your name can be no longer than 100 characters.";
    }
    // Validate Email
    if (empty($formData['email'])) {
      $this->request->session['errors']['email'] = "You must supply an email address.";
    }
    elseif (strlen($formData['email']) > 100) {
      $this->request->session['errors']['email'] = "The email address can be no longer than 100 characters.";
    }
    elseif (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
      $this->request->session['errors']['email'] = "The email address is not valid.";
    }
    // Validate Inquiry
    if (empty($formData['inquiry'])) {
      $this->request->session['errors']['inquiry'] = "Please enter your inquiry.";
    }
    return empty($this->request->session["errors"]);
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
