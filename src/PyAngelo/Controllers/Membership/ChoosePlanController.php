<?php
namespace PyAngelo\Controllers\Membership;

use NumberFormatter;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Utilities\CountryDetector;
use PyAngelo\Repositories\StripeRepository;
use PyAngelo\Repositories\CountryRepository;

class ChoosePlanController extends Controller {
  protected $stripeRepository;
  protected $countryRepository;
  protected $countryDetector;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    StripeRepository $stripeRepository,
    CountryRepository $countryRepository,
    CountryDetector $countryDetector,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->stripeRepository = $stripeRepository;
    $this->countryRepository = $countryRepository;
    $this->countryDetector = $countryDetector;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->showLoginOptions();

    $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
    $currency = $this->getCurrency();
    $membershipPrices = $this->stripeRepository->getMembershipPrices($currency['currency_code']);

    $hasActiveSubscription = $this->auth->hasActiveSubscription();
    if ($hasActiveSubscription)
      return $this->redirectToSubscriptionPage();

    $this->response->setView('membership/choose-plan.html.php');

    $this->response->setVars(array(
      'pageTitle' => 'Get Full Access to all PyAngelo Tutorials',
      'metaDescription' => 'A monthly subscription will give you full access to every tutorial on the PyAngelo website.',
      'activeLink' => 'Choose Plan',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'currency' => $currency,
      'membershipPrices' => $membershipPrices,
      'stripePublishableKey' => $this->request->env['STRIPE_PUBLISHABLE_KEY'],
      'numberFormatter' => $this->numberFormatter
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function showLoginOptions() {
    $this->response->setView('membership/choose-plan-not-logged-in.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Get Full Access to all PyAngelo Tutorials',
      'metaDescription' => 'A monthly subscription will give you full access to every tutorial on the PyAngelo website.',
      'activeLink' => 'Choose Plan',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
    ));
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToSubscriptionPage() {
    $this->flash('You already have full access with your current subscription!', 'warning');
    $this->response->header('Location: /subscription');
    return $this->response;
  }

  private function getCurrency() {
    if ($this->auth->loggedIn()) {
      $countryCode = $this->auth->person()['country_code'];
    }
    else {
       $countryCode = $this->countryDetector->getCountryFromIp();
    }

    return $this->countryRepository->getCurrencyFromCountryCode($countryCode);
  }
}
