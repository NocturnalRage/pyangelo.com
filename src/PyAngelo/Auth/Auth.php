<?php
namespace PyAngelo\Auth;

use PyAngelo\Repositories\PersonRepository;
use Framework\Request;

class Auth {
  protected $loggedIn = false;
  protected $person;
  protected $personRepository;
  protected $request;

  public function __construct(
    PersonRepository $personRepository,
    Request $request
  ) {
    $this->personRepository = $personRepository;
    $this->request = $request;

    if ($this->loggedIn = $this->setLoginStatus()) {
      $this->person = $this->personRepository->getPersonByEmail(
        $request->session['loginEmail']
      );
    }
  }

  public function loggedIn() {
    return $this->loggedIn;
  }

  public function isAdmin() {
    if ($this->loggedIn) {
      return $this->person['admin'] == 1 ? true : false;
    }
    return false;
  }

  public function unreadNotificationCount() {
    return $this->loggedIn ? $this->personRepository->unreadNotificationCount($this->person['person_id'])['unread'] : 0;
  }

  public function person() {
    return $this->person;
  }

  public function personId() {
    return $this->person['person_id'] ?? 0;
  }

  public function stripeCustomerId() {
    return $this->person['stripe_customer_id'] ?? NULL;
  }

  public function insertRememberMe($personId, $session, $tokenHash) {
    return $this->personRepository->insertRememberMe($personId, $session, $tokenHash);
  }

  public function deleteRememberMe() {
    return $this->personRepository->deleteRememberMe($this->person['person_id']);
  }

  public function createCrsfToken() {
    if (isset($this->request->session['crsfToken'])) {
      $crsfToken = $this->request->session['crsfToken'];
    }
    else {
      $crsfToken = bin2hex(openssl_random_pseudo_bytes(32));
      $this->request->session['crsfToken'] = $crsfToken;
    }
    return $crsfToken;
  }

  public function crsfTokenIsValid() {
    // Validate the CRSF token
    if (
      empty($this->request->post['crsfToken']) ||
      empty($this->request->session['crsfToken']) ||
      $this->request->post['crsfToken'] != $this->request->session['crsfToken']
    ) {
      return FALSE;
    }
    else {
        return TRUE;
    }
  }

  public function setLoginStatus() {
    if ($this->loggedInThroughSession()) {
      $this->loggedIn = TRUE;
    }
    else if ($this->loggedInThroughRememberMe()) {
      $this->setSessionLoginEmail($this->request->cookie['rememberme']);
      $this->personRepository->updateLastLogin($this->request->cookie['rememberme']);
      $this->loggedIn = TRUE;
    }
    else {
      $this->loggedIn = FALSE;
    }
    return $this->loggedIn;
  }

  private function setSessionLoginEmail($personId) {
    $this->person = $this->personRepository->getPersonById($personId);
    $this->request->session['loginEmail'] = $this->person['email'];
  }

  private function loggedInThroughSession() {
    return isset($this->request->session['loginEmail']);
  }

  private function loggedInThroughRememberMe() {
    if (isset($this->request->cookie['rememberme']) &&
        isset($this->request->cookie['remembermesession']) &&
        isset($this->request->cookie['remembermetoken'])) {
      if ($this->authenticateRememberMeCookie(
        $this->request->cookie['rememberme'],
        $this->request->cookie['remembermesession'],
        $this->request->cookie['remembermetoken'])
      ) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    else
      return FALSE;
  }

  private function authenticateRememberMeCookie($personId, $sessionId, $token) {
    $rememberMeCookie = $this->personRepository->getRememberMe($personId, $sessionId);
    if (password_verify($token, $rememberMeCookie['token'])) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function authenticateLogin($loginEmail, $loginPassword) {
    $person = $this->personRepository->getPersonByEmail($loginEmail);
    if (! $person) {
      $this->loggedIn = FALSE;
    }
    else if (password_verify($loginPassword, $person['password'])) {
      // Log the user in
      $this->request->session['loginEmail'] = $person['email'];
      $personId = $person['person_id'];
      $this->personRepository->updateLastLogin($personId);
      $this->person = $this->personRepository->getPersonByEmail($loginEmail);
      $this->loggedIn = TRUE;
    }
    else {
      $this->loggedIn = FALSE;
    }
    return $this->loggedIn;
  }

  public function isPasswordResetTokenValid($token) {
    $resetRequest = $this->personRepository->getPasswordResetRequest($token);
    if ($resetRequest)
      return TRUE;
    else
      return FALSE;
  }

  public function hasActiveSubscription() {
    $sub = $this->personRepository->getActiveSubscriptionCount($this->personId());
    if ($sub['active_subscription_count'] == 0) {
      return false;
    }
    else {
      return true;
    }
  }

  public function createNotification(
    $personId,
    $notificationTypeId,
    $notificationType,
    $data
  ) {
    $this->personRepository->createNotification(
      $personId,
      $notificationTypeId,
      $notificationType,
      $data
    );
  }

  public function impersonating() {
    return isset($this->request->session['impersonator']);
  }

  public function getPersonDetailsForViews() {
    return [
      'loggedIn' => $this->loggedIn,
      'details' => $this->person,
      'isAdmin' => $this->isAdmin(),
      'isImpersonating' => $this->impersonating(),
      'crsfToken' => $this->createCrsfToken(),
      'unreadNotificationCount' => $this->unreadNotificationCount()
    ];
  }
}
?>
