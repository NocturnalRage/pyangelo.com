<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class QuizzesRecordResponseController extends Controller {
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
    $this->response->setView('quizzes/record.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must record a response from the PyAngelo website.')
      ));
      return $this->response;
    }

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must be logged in to record a response.')
      ));
      return $this->response;
    }

    if (!isset($this->request->post['tutorialQuizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must select a quiz to record a response for.')
      ));
      return $this->response;
    }

    if (!isset($this->request->post['skillQuestionId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must select a quiz question to record a response for.')
      ));
      return $this->response;
    }
    if (!isset($this->request->post['skillQuestionOptionId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must select a response to record it.')
      ));
      return $this->response;
    }
    if (!isset($this->request->post['correctUnaided'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Did not receive the correct or incorrect flag.')
      ));
      return $this->response;
    }
    if (!isset($this->request->post['questionStartTime'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Did not receive the start time for the question.')
      ));
      return $this->response;
    }
    if (!isset($this->request->post['questionEndTime'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Did not receive the end time for the question.')
      ));
      return $this->response;
    }

    if (! $options = $this->tutorialRepository->getTutorialQuizOptions($this->request->post['tutorialQuizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must select a valid quiz to record a response for.')
      ));
      return $this->response;
    }

    if ($this->auth->personId() != $options[0]["person_id"]) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('You must select your own quiz to record a response.')
      ));
      return $this->response;
    }

    if (! $this->tutorialRepository->updateTutorialQuizQuestion(
            $this->request->post['tutorialQuizId'],
            $this->request->post['skillQuestionId'],
            $this->request->post['skillQuestionOptionId'],
            $this->request->post['correctUnaided'],
            $this->request->post['questionStartTime'],
            $this->request->post['questionEndTime']
          )
    ) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'message' => json_encode('Could not record response.')
      ));
      return $this->response;
    }

    $this->response->setVars(array(
        'status' => json_encode('success'),
        'message' => json_encode('Response recorded')
      ));
    return $this->response;
  }
}
