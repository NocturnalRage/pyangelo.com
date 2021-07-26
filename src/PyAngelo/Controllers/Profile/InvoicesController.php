<?php
namespace PyAngelo\Controllers\Profile;

use NumberFormatter;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class InvoicesController extends Controller {
  protected $personRepository;
  protected $numberFormatter;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    NumberFormatter $numberFormatter
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->numberFormatter = $numberFormatter;
  }

  public function exec() {
    if (! $this->auth->loggedIn()) {
      $this->flash('You must be logged in to view your invoices.', 'danger');
      $this->response->header('Location: /login');
      return $this->response;
    }

    $payments = $this->personRepository->getPaymentHistory(
      $this->auth->personId()
    );

    $this->response->setView('profile/invoices.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'PyAngelo Invoices',
      'metaDescription' => 'Your payment history with PyAngelo.',
      'activeLink' => 'invoices',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'payments' => $payments,
      'numberFormatter' => $this->numberFormatter
    ));
    return $this->response;
  }
}
