<?php
namespace PyAngelo\Controllers\Admin;

use DateTime;
use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\PersonRepository;

class UpdateEndDateController extends Controller {
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
    if (!$this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! isset($this->request->post['person_id'])) {
      $this->flash('You must select a person in order to grant them premium member access!', 'danger');
      $this->response->header('Location: /admin/users');
      return $this->response;
    }

    if (! $person = $this->personRepository->getPersonByIdForAdmin($this->request->post['person_id'])) {
      $this->flash('You must select a valid person in order to grant them premium member access!', 'danger');
      $this->response->header('Location: /admin/users');
      return $this->response;
    }

    if (! isset($this->request->post['months'])) {
      $this->flash('You must select the number of months access you wish to grant.', 'danger');
      $this->response->header('Location: /admin/users/' . $this->request->post['person_id']);
      return $this->response;
    }
    if (
      $this->request->post['months'] != 0 &&
      $this->request->post['months'] != 1 &&
      $this->request->post['months'] != 3 &&
      $this->request->post['months'] != 12 &&
      $this->request->post['months'] != 120
    ) {
      $this->flash('The number of months access must be 0, 1, 12, or 120.', 'danger');
      $this->response->header('Location: /admin/users/' . $this->request->post['person_id']);
      return $this->response;
    }

    // All data is valid
    $futureDate = new DateTime();
    if ($this->request->post['months'] > 0) {
      $futureDate = $futureDate->modify('+' . $this->request->post['months'] . ' month');
    }
    $futureDate = $futureDate->format('Y-m-d H:i:s');
    $this->personRepository->updatePremiumEndDate(
      $this->request->post['person_id'],
      $futureDate
    );
    if ($this->request->post['months'] > 0) {
      $this->subscribeToPremiumNewsletter($this->request->post['person_id']);
      $this->flash('Access has been granted.', 'success');
    }
    else {
      $this->unsubscribeFromPremiumNewsletter($this->request->post['person_id']);
      $this->flash('Access has been revoked.', 'success');
    }
    $this->response->header('Location: /admin/users/' . $this->request->post['person_id']);

    return $this->response;
  }

  private function subscribeToPremiumNewsletter($personId) {
    $premiumListId = 2;
    $activeStatus = 1;
    $subscriber = $this->personRepository->getSubscriber($premiumListId, $personId);
    if (!$subscriber) {
      $this->personRepository->insertSubscriber($premiumListId, $personId);
    }
    else if ($subscriber['subscriber_status_id'] != 1) {
      $this->personRepository->updateSubscriber(
        $premiumListId,
        $personId,
        $activeStatus
      );
    }
  }

  private function unsubscribeFromPremiumNewsletter($personId) {
    $premiumListId = 2;
    $unsubscribedStatus = 2;
    $this->personRepository->updateSubscriber(
      $premiumListId,
      $personId,
      $unsubscribedStatus
    );
  }
}
