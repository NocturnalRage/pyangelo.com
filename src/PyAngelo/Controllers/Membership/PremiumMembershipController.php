<?php
namespace PyAngelo\Controllers\Membership;

use NumberFormatter;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Utilities\CountryDetector;
use PyAngelo\Repositories\StripeRepository;
use PyAngelo\Repositories\CountryRepository;

class PremiumMembershipController extends Controller {
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
    $currency = $this->getCurrency();
    $membershipPrices = $this->stripeRepository->getMembershipPrices($currency['currency_code']);

    $_SESSION['redirect'] = $this->request->server['REQUEST_URI'];
    $this->response->setView('membership/premium-membership.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Become a PyAngelo Premium Member',
      'metaDescription' => 'Sign up to a subscription to become a premium member of the PyAngelo website. This will give you full access to every tutorial on the website.',
      'activeLink' => 'Premium Membership',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'hasActiveSubscription' => $this->auth->hasActiveSubscription(),
      'currency' => $currency,
      'membershipPrices' => $membershipPrices,
      'stripePublishableKey' => $this->request->env['STRIPE_PUBLISHABLE_KEY'],
      'numberFormatter' => $this->numberFormatter
    ));
    $this->addVar('flash');
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
