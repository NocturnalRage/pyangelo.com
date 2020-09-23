<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\{Request, Response};

class AskTheTeacherToggleAlertController extends Controller {
  protected $questionRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuestionRepository $questionRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->questionRepository = $questionRepository;
  }

  public function exec() {
    $this->response->setView('ask-the-teacher/toggle-alert.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update your notifications'
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'Please update your notifications from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['questionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a question to be notified about.'
      ));
      return $this->response;
    }

    if (! $blog = $this->questionRepository->getQuestionById($this->request->post['questionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid question to be notified about.'
      ));
      return $this->response;
    }

    $alertUser = $this->questionRepository->shouldUserReceiveAlert(
      $this->request->post['questionId'],
      $this->auth->personId()
    );

    if (! $alertUser) {
      $this->questionRepository->addToQuestionAlert(
        $this->request->post['questionId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Notifications are on for this question'
      ));
    }
    else {
      $this->questionRepository->removeFromQuestionAlert(
        $this->request->post['questionId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Notifications are off for this question'
      ));
    }
    return $this->response;
  }
}
