<?php
namespace PyAngelo\Controllers\Profile;

use NumberFormatter;
use Carbon\Carbon;
use Framework\{Request, Response};
use PyAngelo\Repositories\StripeRepository;
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class SubscriptionController extends Controller {
  protected $stripeRepository;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeRepository $stripeRepository,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeRepository = $stripeRepository;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to view your subscription information.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    $this->response->setView('profile/subscription.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Subscription Information',
      'metaDescription' => 'This page lists any subscriptions you have with PyAngelo. You can update or cancel an existing subscription from this page.',
      'activeLink' => 'subscription',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'numberFormatter' => $this->numberFormatter
    ));

    $subscription = $this->stripeRepository->getCurrentSubscription(
      $this->auth->personId()
    );
    $pastSubscriptions = $this->stripeRepository->getPastSubscriptions(
      $this->auth->personId()
    );
    if ($subscription) {
      $subscription['premiumMemberSince'] = Carbon::createFromFormat('Y-m-d H:i:s', $subscription['start_date'])->diffForHumans();
      $subscription['nextPaymentDate'] = Carbon::createFromFormat('Y-m-d H:i:s', $subscription['current_period_end'])->diffForHumans();
      $this->response->addVars(array(
        'subscription' => $subscription
      ));
    }
    if ($pastSubscriptions) {
      $this->response->addVars(array(
        'pastSubscriptions' => $pastSubscriptions
      ));
    }
    $this->addVar('flash');
    return $this->response;
  }
}
