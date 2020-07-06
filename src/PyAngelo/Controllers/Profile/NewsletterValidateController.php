<?php
namespace PyAngelo\Controllers\Profile;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;
use PyAngelo\Repositories\CampaignRepository;
use Framework\{Request, Response};

class NewsletterValidateController extends Controller {
  protected $personRepository;
  protected $campaignRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    PersonRepository $personRepository,
    CampaignRepository $campaignRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->personRepository = $personRepository;
    $this->campaignRepository = $campaignRepository;
  }

  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (! $this->auth->crsfTokenIsValid())
      return $this->redirectToNewsletterPage();

    $this->updateSubscriberStatus();

    $this->flash('Your preference has been updated.', 'success');
    $this->response->header("Location: /newsletter");
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->flash('You must be logged in to change your email newsletter preferences.', 'danger');
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToNewsletterPage() {
    $this->flash('Please update your preferences from the PyAngelo website.', 'danger');
    $this->response->header('Location: /newsletter');
    return $this->response;
  }

  function updateSubscriberStatus() {
    $subscriberStatusId = empty($this->request->post['newsletter']) ? 2 : 1;
    $personId = $this->auth->personId();

    $lists = $this->campaignRepository->getAllLists();
    foreach ($lists as $list) {
      $this->personRepository->updateSubscriber(
        $list['list_id'],
        $personId,
        $subscriberStatusId
      );
    }
  }
}
