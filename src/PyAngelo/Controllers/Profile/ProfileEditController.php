<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\CountryRepository;

class ProfileEditController extends Controller {
  protected $countryRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    CountryRepository $countryRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->countryRepository = $countryRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to edit your profile.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    $formVars = $_SESSION['formVars'] ?? $this->auth->person();
    unset($_SESSION['formVars']);

    $this->response->setView('profile/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit Profile',
      'metaDescription' => 'Edit your PyAngelo profile.',
      'activeLink' => 'profile',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'formVars' => $formVars,
      'countries' => $this->countryRepository->getRealCountries()
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }
}
