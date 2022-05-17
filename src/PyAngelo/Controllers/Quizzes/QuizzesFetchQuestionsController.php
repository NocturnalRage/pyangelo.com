<?php
namespace PyAngelo\Controllers\Quizzes;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuizRepository;

class QuizzesFetchQuestionsController extends Controller {
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
    $this->response->setView('quizzes/options.json.php');
    $this->response->header('Content-Type: application/json');

    if (! $this->auth->loggedIn()) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'options' => json_encode([]),
        'message' => json_encode('You must be logged in to fetch your quiz options.')
      ));
      return $this->response;
    }

    if (!isset($this->request->get['quizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'options' => json_encode([]),
        'message' => json_encode('You must select a quiz to fetch options for.')
      ));
      return $this->response;
    }

    if (! $options = $this->quizRepository->getQuizOptions($this->request->get['quizId'])) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'options' => json_encode([]),
        'message' => json_encode('You must select a valid quiz to fetch options for.')
      ));
      return $this->response;
    }

    if ($this->auth->personId() != $options[0]["person_id"]) {
      $this->response->setVars(array(
        'status' => json_encode('error'),
        'options' => json_encode([]),
        'message' => json_encode('You must select your own quiz.')
      ));
      return $this->response;
    }

    $answers = [];
    $quizOption = [];
    $currentQuestion = $options[0]["question"];
    $currentQuestionImage = $options[0]["question_image"];
    $currentSkillQuestionId = $options[0]["skill_question_id"];
    $currentSkillQuestionTypeId = $options[0]["skill_question_type_id"];

    foreach ($options as $option) {
      if ($option["skill_question_id"] != $currentSkillQuestionId) {
        $hints = $this->quizRepository->getSkillQuestionHints(
          $currentSkillQuestionId
        );
        $quizOptions[] = [
            "question" => $currentQuestion,
            "question_image" => $currentQuestionImage,
            "skill_question_id" => $currentSkillQuestionId,
            "skill_question_type_id" => $currentSkillQuestionTypeId,
            "answers" => $quizOption,
            "hints" => $hints,
        ];
        $quizOption = [];
        $currentQuestion = $option["question"];
        $currentQuestionImage = $option["question_image"];
        $currentSkillQuestionId = $option["skill_question_id"];
        $currentSkillQuestionTypeId = $option["skill_question_type_id"];
      }
      $quizOption[] = [
        "skill_question_option_id" => $option["skill_question_option_id"],
        "option" => $option["option_text"],
        "option_order" => $option["option_order"],
        "correct" => $option["correct"]
      ];
    }
    $hints = $this->quizRepository->getSkillQuestionHints(
      $currentSkillQuestionId
    );
    $quizOptions[] = [
      "question" => $currentQuestion,
      "question_image" => $currentQuestionImage,
      "skill_question_id" => $currentSkillQuestionId,
      "skill_question_type_id" => $currentSkillQuestionTypeId,
      "answers" => $quizOption,
      "hints" => $hints
    ];

    // Give questions in random order
    shuffle($quizOptions);
    $this->response->setVars(array(
        'status' => json_encode('success'),
        'options' => json_encode($quizOptions),
        'message' => json_encode('Questions retrieved')
      ));
    return $this->response;
  }
}
