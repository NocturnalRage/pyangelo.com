<?php
namespace PyAngelo\Controllers\Profile;

use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class NotificationsController extends Controller {
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
      return $this->RedirectToLoginPage();

    if ($this->request->get['all'] ?? 0) {
      $selection = 'all';
      $notifications = $this->personRepository->getAllNotifications(
        $this->auth->personId()
      );
    }
    else {
      $selection = 'unread';
      $notifications = $this->personRepository->getUnreadNotifications(
        $this->auth->personId()
      );
    }

    $this->response->setView('profile/notifications.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Notifications',
      'metaDescription' => 'Notifications from blogs, lessons, and questions.',
      'activeLink' => 'profile',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'notifications' => $notifications,
      'selection' => $selection
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function RedirectToLoginPage() {
    $this->flash('You must be logged in to view your notifications.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
