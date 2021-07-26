<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use Framework\Billing\StripeWrapper;
use PyAngelo\Repositories\StripeRepository;
use PyAngelo\Auth\Auth;
use PyAngelo\Email\WhyCancelEmail;
use PyAngelo\Controllers\Controller;

class CancelSubscriptionController extends Controller {
  protected $stripeWrapper;
  protected $stripeRepository;
  protected $whyCancelEmail;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeWrapper $stripeWrapper,
    StripeRepository $stripeRepository,
    WhyCancelEmail $whyCancelEmail
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeWrapper = $stripeWrapper;
    $this->stripeRepository = $stripeRepository;
    $this->whyCancelEmail = $whyCancelEmail;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to cancel your subscription.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please cancel your subscription from the PyAngelo website.', 'danger');
      $this->response->header('Location: /subscription');
      return $this->response;
    }

    $person = $this->auth->person();

    try {
      $subscription = $this->stripeRepository->getCurrentSubscription($person['person_id']);
      if (! $subscription) {
        throw new \Exception('You do not have an active subscription.');;
      }
      $canceled = $this->stripeWrapper->cancelSubscription($subscription['subscription_id']);
      $this->stripeRepository->updateSubscriptionStatus(
        $canceled->id, $canceled->status
      );
      $this->flash('Your subscription has been canceled.', 'success');

      $mailInfo = [
        'givenName' => $person['given_name'],
        'toEmail' => $person['email']
      ];
      $this->whyCancelEmail->queueEmail($mailInfo);
    } catch (\Exception $e) {
      $message = 
        'Sorry, we could not cancel your subscription. ' .
        'Please try again, or contact us. Here was the error message: ' . 
        $e->getMessage();
      $this->flash($message, 'danger');
    }

    $this->response->header('Location: /subscription');
    return $this->response;
  }
}
