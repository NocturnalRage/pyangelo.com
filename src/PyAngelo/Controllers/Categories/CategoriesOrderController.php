<?php
namespace PyAngelo\Controllers\Categories;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class CategoriesOrderController extends Controller {
  protected $tutorialRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    TutorialRepository $tutorialRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->tutorialRepository = $tutorialRepository;
  }

  public function exec() {
    if (! $this->auth->isAdmin()) {
      $status = 'error';
      $message = 'You are not authorised!';
    }
    else if (!isset($this->request->post['idsInOrder'])) {
      $status = 'error';
      $message = 'The order of the tutorials was not received!';
    }
    else {
      $position = 0;
      foreach ($this->request->post['idsInOrder'] as $slug) {
        $position++;
        $this->tutorialRepository->updateTutorialOrder($slug, $position);
      }
      $status = 'success';
      $message = 'The new order has been saved.';
    }

    $this->response->setView('categories/order.json.php');
    $this->response->header('Content-Type: application/json');
    $this->response->setVars(array(
      'status' => $status,
      'message' => $message
    ));
    return $this->response;
  }
}
