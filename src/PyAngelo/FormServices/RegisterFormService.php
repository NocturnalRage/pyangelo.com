<?php
namespace PyAngelo\FormServices;

use GeoIp2\Database\Reader;
use PyAngelo\Utilities\CountryDetector;
use PyAngelo\Email\ActivateMembershipEmail;
use PyAngelo\Repositories\PersonRepository;

class RegisterFormService {
  protected $errors = [];
  protected $flashMessage;
  protected $claimInactiveUser = false;

  protected $personRepository;
  protected $activateMembershipEmail;
  protected $countryDetector;
  protected $serverInfo;

  public function __construct(
    PersonRepository $personRepository,
    ActivateMembershipEmail $activateMembershipEmail,
    CountryDetector $countryDetector,
    Array $serverInfo
  ) {
    $this->personRepository = $personRepository;
    $this->activateMembershipEmail= $activateMembershipEmail;
    $this->countryDetector = $countryDetector;
    $this->serverInfo = $serverInfo;
  }

  public function createPerson($formData) {
    if (!$this->isInputValidForCreation($formData)) {
      $this->flashMessage = 'There were some errors. ' .
         'Please fix these and then we will create your free account.';
      return false;
    }

    if ($this->claimInactiveUser) {
      $personId = $this->updatePerson($formData);
    }
    else {
      $personId = $this->insertPerson($formData);
    }

    $this->sendActivateEmail($personId, $formData);
    return true;
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getFlashMessage() {
    return $this->flashMessage;
  }

  private function updatePerson($formData) {
    $countryCode = $this->countryDetector->getCountryFromIp();
    $person = $this->personRepository->getPersonActiveOrNotByEmail($formData['email']);
    $personId = $person['person_id'];
    $this->personRepository->updatePerson(
      $personId,
      $formData['givenName'],
      $formData['familyName'],
      $formData['email'],
      0, // Not active until user confirms email
      $countryCode,
      $countryCode
    );
    $this->personRepository->updatePassword(
      $personId,
      $formData['loginPassword']
    );
    return $personId;
  }
  private function insertPerson($formData) {
    $countryCode = $this->countryDetector->getCountryFromIp();
    return $this->personRepository->insertFreeMember(
      $formData['givenName'],
      $formData['familyName'],
      $formData['email'],
      $formData['loginPassword'],
      $countryCode,
      $countryCode
    );
  }

  private function sendActivateEmail($personId, $formData) {
    $token = bin2hex(random_bytes(32));
    $result = $this->personRepository->insertMembershipActivate(
      $personId,
      $formData['email'],
      $token
    );

    $mailInfo = [
      'requestScheme' => $this->serverInfo['requestScheme'],
      'serverName' => $this->serverInfo['serverName'],
      'token' => $token,
      'givenName' => $formData['givenName'],
      'toEmail' => $formData['email']
    ];
    $this->activateMembershipEmail->sendEmail($mailInfo);
  }

  private function isInputValidForCreation($formData) {
    $valid = true;
    if (!$this->isCommonInputValid($formData)) {
      $valid = false;
    }
    if (!$this->isEmailValidForCreation($formData)) {
      $valid = false;
    }
    if (!$this->isPasswordValidForCreation($formData)) {
      $valid = false;
    }
    return $valid;
  }

  private function isCommonInputValid($formData) {
    if (empty($formData['consent'])) {
      $this->errors['consent'] = "You must agree to the terms of use and privacy policy to create your account.";
    }
    if (empty($formData['givenName'])) {
      $this->errors['givenName'] = "The given name field cannot be blank.";
    }
    else if (strlen($formData['givenName']) > 100) {
      $this->errors["givenName"] = "The given name can be no longer than 100 characters.";
    }

    if (empty($formData['familyName'])) {
      $this->errors['familyName'] = "The family name field cannot be blank.";
    }
    elseif (strlen($formData['familyName']) > 100) {
      $this->errors["familyName"] = "The family name can be no longer than 100 characters.";
    }
    if (!empty($this->errors)) {
      return false;
    }
    return true;
  }

  private function isPasswordValidForCreation($formData) {
    if (empty($formData['loginPassword'])) {
      $this->errors['loginPassword'] = "You must choose a password.";
      return false;
    }
    else {
      $passwordLength = strlen($formData['loginPassword']);
      if ($passwordLength < 4 || $passwordLength > 30) {
        $this->errors['loginPassword'] = "The password must be between 4 and 30 characters in length.";
        return false;
      }
      return true;
    }
  }

  private function checkCommonEmailValidation($formData) {
    if (empty($formData['email'])) {
      $this->errors['email'] = "You must supply an email address.";
      return false;
    }
    if (strlen($formData['email']) > 100) {
      $this->errors['email'] = "The email address can be no longer than 100 characters.";
      return false;
    }
    if (filter_var($formData['email'], FILTER_VALIDATE_EMAIL) === false) {
      $this->errors['email'] = "The email address is not valid.";
      return false;
    }
    return true;
  }

  private function isEmailValidForCreation($formData) {
    if (!$this->checkCommonEmailValidation($formData)) {
      return false;
    }
    $person = $this->personRepository->getPersonActiveOrNotByEmail($formData['email']);
    if ($person) {
      if ($person['active'] == 1) {
        $this->errors["email"] = "This email address is already taken. Please enter another email address.";
        return false;
      }
      elseif ($this->previouslyBounced($person['email_status_id']))  {
        $this->errors["email"] = "We've already sent an activation email to this address and it bounced. This means there is something wrong with this email account. I suggest trying a different email address.";
        return false;
      }
      else {
        $this->claimInactiveUser = true;
      }
    }
    return true;
  }

  private function previouslyBounced($emailStatusId) {
    return $emailStatusId == 1 ? FALSE : TRUE;
  }
}
