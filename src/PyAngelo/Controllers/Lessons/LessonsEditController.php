<?php
namespace PyAngelo\Controllers\Lessons;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class LessonsEditController extends Controller {
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
    if (!$this->auth->isAdmin())
      return $this->redirectToHomePageWithWarning();

    if (! $lesson = $this->getLessonFromSlugs())
      return $this->redirectToPageNotFound();

    $formVars = $this->request->session['formVars'] ?? $lesson;
    unset($this->request->session['formVars']);

    $this->response->setView('lessons/edit.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Edit Lesson' ,
      'metaDescription' => 'Edit the ' . $lesson['lesson_title'] . ' lesson.',
      'activeLink' => 'Tutorials',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'securityLevels' => $this->tutorialRepository->getAllLessonSecurityLevels(),
      'lesson' => $lesson,
      'submitButtonText' => 'Update',
      'formVars' => $formVars
    ));
    $this->addVar('errors');
    $this->addVar('flash');
    return $this->response;
  }

  private function getLessonFromSlugs() {
    if (! isset($this->request->get['slug']) ||
        ! isset($this->request->get['lesson_slug'])
    ) {
      return false;
    }

    return $lesson = $this->tutorialRepository->getLessonBySlugs(
      $this->request->get['slug'],
      $this->request->get['lesson_slug']
    );
  }

  private function redirectToHomePageWithWarning() {
    $this->flash('You are not authorised!', 'danger');
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToPageNotFound() {
    $this->response->header('Location: /page-not-found');
    return $this->response;
  }
}
