<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuizRepository;

class QuizzesRecordCompletionController extends Controller {
  protected $quizRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    QuizRepository $quizRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->quizRepository = $quizRepository;
  }

  public function exec() {
    $this->response->setView('quizzes/complete.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->crsfTokenIsValid()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('You must complete the quiz from the PyAngelo website.')
      ));
      return $this->response;
    }

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('You must be logged in to complete a quiz.')
      ));
      return $this->response;
    }

    if (!isset($this->request->post['quizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('You must select a quiz to complete.')
      ));
      return $this->response;
    }

    if (!isset($this->request->post['quizStartTime'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('Did not receive the start time for the quiz.')
      ));
      return $this->response;
    }
    if (!isset($this->request->post['quizEndTime'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('Did not receive the end time for the quiz.')
      ));
      return $this->response;
    }

    if (! $options = $this->quizRepository->getQuizOptions($this->request->post['quizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('You must select a valid quiz to complete.')
      ));
      return $this->response;
    }

    if ($this->auth->personId() != $options[0]["person_id"]) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('You must select your own quiz to complete.')
      ));
      return $this->response;
    }

    if (! $this->quizRepository->updateQuiz(
            $this->request->post['quizId'],
            $this->request->post['quizStartTime'],
            $this->request->post['quizEndTime']
          )
    ) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('Could not record response.')
      ));
      return $this->response;
    }
    // Update Skills
    if (! $resultsSkillsMatrix = $this->quizRepository->getQuizResultsAndSkillMastery($this->request->post['quizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'skillsMatrix' => json_encode([]),
        'message' => json_encode('Could not retrieve skills.')
      ));
      return $this->response;
    }
    $skillsMatrix = [];
    foreach($resultsSkillsMatrix as $result) {
      $result["mastery_level_id"] = (int) $result["mastery_level_id"];
      $result["correct"] = (int) $result["correct"];
      $result["total"] = (int) $result["total"];
      $previousMastery = (int) $result["mastery_level_id"];
      $percent = $result["correct"]/$result["total"];
      $quizTypeId = $result["quiz_type_id"];
      $skillQuizType = 1;
      $tutorialQuizType = 2;
      if ($percent >= 1) {
        if ($previousMastery == 4) {
          $mastery = 4;
          $mastery_desc = "Mastered";
        }
        elseif ($previousMastery == 3 && $quizTypeId == $tutorialQuizType) {
          $mastery = 4;
          $mastery_desc = "Mastered";
        }
        else {
          $mastery = 3;
          $mastery_desc = "Proficent";
        }
      }
      elseif ($percent >= 0.7) {
        $mastery = 2;
        $mastery_desc = "Familiar";
      }
      else {
        $mastery = 1;
        $mastery_desc = "Attempted";
      }
      $result["new_mastery_level_id"] = $mastery;
      $result["new_mastery_level_desc"] = $mastery_desc;
      $skillsMatrix[] = $result;

      $this->quizRepository->insertOrUpdateSkillMastery(
        $result['skill_id'],
        $this->auth->personId(),
        $mastery
      );
    }

    $this->response->setVars(array(
        'status' => json_encode('success'),
        'skillsMatrix' => json_encode($skillsMatrix),
        'message' => json_encode('Completion recorded')
      ));
    return $this->response;
  }
}
