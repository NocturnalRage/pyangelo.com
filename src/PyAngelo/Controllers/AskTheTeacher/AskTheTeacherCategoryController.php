<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\QuestionRepository;

class AskTheTeacherCategoryController extends Controller {
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
    if (!isset($this->request->get['slug'])) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    if (! $category = $this->questionRepository->getCategoryBySlug(
      $this->request->get['slug']
    )) {
      $this->response->header('Location: /page-not-found');
      return $this->response;
    }

    $questions = $this->questionRepository->getCategoryQuestionsBySlug(
      $this->request->get['slug']
    );

    $this->response->setView('ask-the-teacher/category.html.php');
    $this->response->setVars(array(
      'pageTitle' => "Coding Questions on {$category['description']}",
      'metaDescription' => "Coding questions on {$category['description']} answered by teachers.",
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'category' => $category,
      'questions' => $questions
    ));
    return $this->response;
  }
}
