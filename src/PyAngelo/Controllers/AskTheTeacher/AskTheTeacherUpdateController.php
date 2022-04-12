<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;
use Framework\Contracts\AvatarContract;

class AskTheTeacherUpdateController extends Controller {
  protected $questionRepository;
  protected $avatar;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    AvatarContract $avatar,
    QuestionRepository $questionRepository
  ) {
    parent::__construct($request, $response, $auth);
    $this->avatar = $avatar;
    $this->questionRepository = $questionRepository;
  }

  public function exec() {
    if (! $this->auth->isAdmin()) {
      $this->flash('You are not authorised!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (! $question = $this->getQuestionBySlug()) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please answer the question from the PyAngelo website!', 'danger');
      $this->response->header('Location: /');
      return $this->response;
    }

    if (empty($this->request->post['question_title'])) {
      $_SESSION['errors']['question_title'] = 'You must supply a title for this question.';
    }
    else if (strlen($this->request->post['question_title']) > 100) {
      $_SESSION['errors']['question_title'] = 'The title must be no more than 100 characters.';
    }
    if (empty($this->request->post['question'])) {
      $_SESSION['errors']['question'] = 'There must be a question.';
    }
    if (empty($this->request->post['answer'])) {
      $_SESSION['errors']['answer'] = 'You must answer the question.';
    }

    if (empty($this->request->post['question_type_id'])) {
      $_SESSION['errors']['question_type_id'] = 'You must select the type of question.';
    }

    if (! empty($_SESSION['errors'])) {
      $this->flash('There were some errors. Please fix these below and then submit your answer again.', 'danger');
      $_SESSION['formVars'] = $this->request->post;
      $this->response->header('Location: /ask-the-teacher/' . $question['slug'] . '/edit');
      return $this->response;
    }
    $alreadyAnswered = FALSE;
    if (! empty($question['answered_at'])) {
      $alreadyAnswered = TRUE;
    }

    $slug = $question['slug'];
    $teacher = $this->auth->person();
    if (! $alreadyAnswered) {
      if ($question['question_title'] != $this->request->post['question_title']) {
        $slug = $this->generateSlug($this->request->post['question_title']);
      }
      $answeredAt = date('Y-m-d H:i:s');
      $teacherId = $teacher['person_id'];
    }
    else {
      $answeredAt = $question['answered_at'];
      $teacherId = $question['teacher_id'];
    }

    $rowsUpdated = $this->questionRepository->answerQuestion(
      $question['question_id'],
      $this->request->post['question_title'],
      $this->request->post['question'],
      $this->request->post['answer'],
      $this->request->post['question_type_id'],
      $teacherId,
      $slug,
      $answeredAt
    );
    if (! $alreadyAnswered) {
      $this->notifyUser($question, $teacher, $slug);
    }

    $this->response->header('Location: /ask-the-teacher/' . $slug);
    return $this->response;
  }

  private function getQuestionBySlug() {
    if (! isset($this->request->post['slug'])) {
      return false;
    }

    return $this->questionRepository->getQuestionBySlug(
      $this->request->post['slug']
    );
  }

  private function generateSlug($title) {
    $slug = substr($title, 0, 100);
    $slug = strtolower($slug);
    $slug = str_replace('.', '-', $slug);
    $slug = preg_replace('/[^a-z0-9 ]/', '', $slug);
    $slug = preg_replace('/\s+/', '-', $slug);
    $slug = trim($slug, '-');
    $slugVersion = 1;
    $unversionedSlug = $slug;
    while ($this->questionRepository->getQuestionBySlug($slug)) {
      $slugVersion++;
      $slug = $unversionedSlug . '-' . $slugVersion;
    }
    return $slug;
  }

  private function notifyUser($question, $teacher, $slug) {
    $avatarUrl = $this->avatar->getAvatarUrl($teacher['email']);
    $commentLink = '/ask-the-teacher/' . $slug;
    $data_json = json_encode([
      'message' => $teacher['given_name'] . ' ' . $teacher['family_name'] . ' answered your question "' . $question['question_title'] . '"',
      'link' => $commentLink,
      'avatarUrl' => $avatarUrl,
      'isAdmin' => $this->auth->isAdmin()
    ]);
    $this->auth->createNotification(
      $question['person_id'],
      $question['question_id'],
      'question',
      $data_json
    );
  }
}
