<?php
namespace PyAngelo\FormServices;

use Framework\Request;
use PyAngelo\Auth\Auth;
use PyAngelo\Email\ForgotPasswordEmail;
use PyAngelo\Repositories\PersonRepository;

class ForgotPasswordFormService {
  protected $errors = [];
  protected $flashMessage;

  protected $personRepository;
  protected $forgotPasswordEmail;
  protected $serverInfo;

  public function __construct(
    PersonRepository $personRepository,
    forgotPasswordEmail $forgotPasswordEmail,
    Array $serverInfo
  ) {
    $this->personRepository = $personRepository;
    $this->forgotPasswordEmail= $forgotPasswordEmail;
    $this->serverInfo = $serverInfo;
  }

  public function saveRequestAndSendEmail($formVars) {
    if (!$this->isEmailValid($formVars)) {
      $this->flashMessage = 'The email was not a valid address. Please check it and submit the form again so we can send you a password reset link.';
      return false;
    }
    $this->process($formVars['email']);
    return true;
  }

  private function process($email) {
    if ($person = $this->personRepository->getPersonByEmail($email)) {
      $token = $this->getToken();
      $result = $this->personRepository->insertPasswordResetRequest(
        $person['person_id'], $token
      );
      $mailInfo = [
        'requestScheme' => $this->serverInfo['requestScheme'],
        'serverName' => $this->serverInfo['serverName'],
        'token' => $token,
        'givenName' => $person['given_name'],
        'toEmail' => $email
      ];
      $this->forgotPasswordEmail->sendEmail($mailInfo);
    }
  }

  private function isEmailValid($formData) {
    if (empty($formData['email'])) {
      $this->errors['email'] = "You must enter the email you used to create your account in order to reset your password.";
      return false;
    }
    if (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
      $this->errors['email'] = "The email you entered is not a valid addresss.";
      return false;
    }
    return true;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getFlashMessage() {
    return $this->flashMessage;
  }

  private function getToken() {
    return bin2hex(random_bytes(32));
  }
}
