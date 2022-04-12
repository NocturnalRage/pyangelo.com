<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Utilities\CountryDetector;
use PyAngelo\Repositories\PersonRepository;
use PyAngelo\Repositories\CountryRepository;
use Framework\Billing\StripeWrapper;

class ProfileUpdateController extends Controller {
  protected $personRepository;
  protected $countryRepository;
  protected $countryDetector;
  protected $stripeWrapper;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    CountryRepository $countryRepository,
    CountryDetector $countryDetector,
    StripeWrapper $stripeWrapper
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->countryRepository = $countryRepository;
    $this->countryDetector = $countryDetector;
    $this->stripeWrapper = $stripeWrapper;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to edit your profile.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please update your profile from the PyAngelo website.', 'danger');
      $this->response->header('Location: /profile');
      return $this->response;
    }
    $_SESSION['formVars'] = $this->request->post;

    if (empty($this->request->post['given_name'])) {
      $_SESSION['errors']['given_name'] = "The given name field cannot be blank.";
    }
    else if (strlen($this->request->post['given_name']) > 100) {
      $_SESSION['errors']["given_name"] = "The given name can be no longer than 100 characters.";
    }

    if (empty($this->request->post['family_name'])) {
      $_SESSION['errors']['family_name'] = "The family name field cannot be blank.";
    }
    elseif (strlen($this->request->post['family_name']) > 100) {
      $_SESSION['errors']["family_name"] = "The family name can be no longer than 100 characters.";
    }

    if (empty($this->request->post['email'])) {
      $_SESSION['errors']["email"] = "You must supply an email address.";
    }
    elseif (strlen($this->request->post['email']) > 100) {
      $_SESSION['errors']["email"] = "The email address can be no longer than 100 characters.";
    }
    elseif (filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL) === false) {
      $_SESSION['errors']["email"] = "The email address is not valid.";
    }
    else {
      $person = $this->personRepository->getPersonActiveOrNotByEmail($this->request->post['email']);
      if ($person) {
        if ($person['person_id'] != $this->auth->personId()) {
          $_SESSION['errors']["email"] = "This email address is already in use.";
        }
      }
    }

    if (empty($this->request->post['country_code'])) {
      $_SESSION['errors']["country_code"] = "You must select the country you are from.";
    }
    elseif (! $country = $this->countryRepository->getCountry($this->request->post['country_code'])) {
      $_SESSION['errors']["country_code"] = "You must select a valid country from the list.";
    }

    if (! empty($_SESSION['errors'])) {
      $this->flash('There were some errors. Please fix these and then we can update your profile.', 'danger');
      $this->response->header('Location: /profile/edit');
      return $this->response;
    }

    $detectedCountryCode = $this->countryDetector->getCountryFromIp();

    $this->personRepository->updatePerson(
      $this->auth->personId(),
      $this->request->post['given_name'],
      $this->request->post['family_name'],
      $this->request->post['email'],
      1,
      $this->request->post['country_code'],
      $detectedCountryCode 
    );

    // Log user in with potentially new email.
    $_SESSION['loginEmail'] = $this->request->post['email'];

    // Update Stripe Email if they have a stripe customer id.
    $existingPerson = $this->auth->person();
    if (
      ! empty($existingPerson['stripe_customer_id']) &&
      $existingPerson['email'] != $this->request->post['email']
    ) {
      $this->stripeWrapper->updateEmail(
        $existingPerson['stripe_customer_id'],
        $this->request->post['email']
      );
    }

    $this->flash('Your profile has been updated.', 'success');
    $this->response->header('Location: /profile');
    return $this->response;
  }
}
