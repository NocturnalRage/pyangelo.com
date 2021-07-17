<?php
namespace PyAngelo\Controllers\Admin;

use NumberFormatter;
use Carbon\Carbon;
use Framework\{Request, Response};
use Framework\Contracts\AvatarContract;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;
use PyAngelo\Repositories\StripeRepository;

class UserController extends Controller {
  protected $personRepository;
  protected $avatar;
  protected $stripeRepository;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    AvatarContract $avatar,
    StripeRepository $stripeRepository,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->avatar = $avatar;
    $this->stripeRepository = $stripeRepository;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! isset($this->request->get['person_id'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    if (! $person = $this->personRepository->getPersonByIdForAdmin($this->request->get['person_id'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    $payments = $this->personRepository->getPaymentHistory(
      $this->request->get['person_id']
    );

    $this->response->setView('admin/user.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Admin User Profile View',
      'metaDescription' => "This page shows details of a person to a PyAngelo administrator.",
      'activeLink' => 'users',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'person' => $person,
      'avatar' => $this->avatar,
      'payments' => $payments,
      'numberFormatter' => $this->numberFormatter
    ));

    $subscription = $this->stripeRepository->getCurrentSubscription(
      $this->request->get['person_id']
    );
    if ($subscription) {
      $subscription['premiumMemberSince'] = Carbon::createFromFormat('Y-m-d H:i:s', $subscription['start'])->diffForHumans();
      $subscription['nextPaymentDate'] = Carbon::createFromFormat('Y-m-d H:i:s', $subscription['current_period_end'])->diffForHumans();
      $this->response->addVars(array(
        'subscription' => $subscription
      ));
    }

    $this->addVar('flash');
    return $this->response;
  }
}
