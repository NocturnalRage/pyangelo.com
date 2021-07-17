<?php
namespace PyAngelo\Controllers\Admin;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\MetricRepository;

class MetricsController extends Controller {
  protected $metricRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    MetricRepository $metricRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->metricRepository = $metricRepository;
  }

  public function exec() {
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    $keyMetrics = $this->metricRepository->getCountMetrics();
    $subscriberGrowth = $this->metricRepository->getSubscriberGrowthByMonth();
    $subscriberPayments = $this->metricRepository->getSubscriberPaymentsByMonth();
    $premiumMembers = $this->metricRepository->getPremiumMemberCountByMonth();
    $plans = $this->metricRepository->getPremiumMemberCountByPlan();
    $premiumCountries = $this->metricRepository->getPremiumMemberCountByCountry();
    $membersMonthly = $this->metricRepository->getMemberCountByMonth();
    $membersDaily = $this->metricRepository->getMemberCountByDay();
    $memberCountries = $this->metricRepository->getMemberCountByCountry();

    $this->response->setView('admin/metrics.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Metrics',
      'metaDescription' => "Track the progress of the PyAngelo website through metrics.",
      'activeLink' => 'metrics',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'keyMetrics' => $keyMetrics,
      'subscriberGrowth' => $subscriberGrowth,
      'subscriberPayments' => $subscriberPayments,
      'premiumMembers' => $premiumMembers,
      'premiumCountries' => $premiumCountries,
      'plans' => $plans,
      'membersMonthly' => $membersMonthly,
      'membersDaily' => $membersDaily,
      'memberCountries' => $memberCountries,
    ));
    $this->addVar('flash');
    return $this->response;
  }
}
