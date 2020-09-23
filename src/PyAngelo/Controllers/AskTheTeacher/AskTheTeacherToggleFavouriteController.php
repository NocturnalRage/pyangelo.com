<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\{Request, Response};

class AskTheTeacherToggleFavouriteController extends Controller {
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
    $this->response->setView('ask-the-teacher/toggle-favourite.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Log in to update your favourites'
      ));
      return $this->response;
    }

    // Is the CRSF Token Valid
    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'Please update your favourites from the PyAngelo website.'
      ));
      return $this->response;
    }

    if (empty($this->request->post['questionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a question to favourite.'
      ));
      return $this->response;
    }

    if (! $blog = $this->questionRepository->getQuestionById($this->request->post['questionId'])) {
      $this->response->setVars(array(
        'status' => 'error',
        'message' => 'You must select a valid question to be favourite.'
      ));
      return $this->response;
    }

    $questionFavourited = $this->questionRepository->getQuestionFavourited(
      $this->request->post['questionId'],
      $this->auth->personId()
    );

    if (! $questionFavourited) {
      $this->questionRepository->addToQuestionFavourited(
        $this->request->post['questionId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'success',
        'message' => 'Question marked as a favourite'
      ));
    }
    else {
      $this->questionRepository->removeFromQuestionFavourited(
        $this->request->post['questionId'],
        $this->auth->personId()
      );
      $this->response->setVars(array(
        'status' => 'info',
        'message' => 'Question removed from favourites'
      ));
    }
    return $this->response;
  }
}
