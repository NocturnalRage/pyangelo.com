<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class NewsletterController extends Controller {
  protected $personRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $subscribed = $this->isUserSubscribed();

    $this->response->setView('profile/newsletter.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Email Newsletter Settings',
      'metaDescription' => 'Update your PyAngelo email newsletter settings.',
      'activeLink' => 'newsletter',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'subscribed' => $subscribed
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to update your email newsletter settings.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function isUserSubscribed() {
    $freeEmailListId = 1;

    $subscriber = $this->personRepository->getSubscriber(
      $freeEmailListId,
      $this->auth->personId()
    );
    if (! $subscriber)
      return false;
    else
      return $subscriber['subscriber_status_id'] == 1 ? true : false;
  }
}
