<?php
namespace PyAngelo\Controllers\Profile;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;
use Framework\Contracts\AvatarContract;

class ProfileController extends Controller {
  protected $personRepository;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    AvatarContract $avatar
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->avatar = $avatar;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $person = $this->auth->person();

    $person['memberSince'] = Carbon::createFromFormat('Y-m-d H:i:s', $person['created_at'])->diffForHumans();

    $this->response->setView('profile/profile.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Profile of ' . $person['given_name'] . ' ' . $person['family_name'],
      'metaDescription' => 'Your PyAngelo profile.',
      'activeLink' => 'profile',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'person' => $person,
      'avatar' => $this->avatar
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to view your profile.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
