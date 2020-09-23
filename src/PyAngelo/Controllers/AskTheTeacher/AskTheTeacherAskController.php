<?php
namespace PyAngelo\Controllers\AskTheTeacher;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;

class AskTheTeacherAskController extends Controller {
  public function exec() {
    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    $this->response->setView('ask-the-teacher/ask.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'Ask the Teacher a Question',
      'metaDescription' => "Ask the teacher a coding question.",
      'activeLink' => 'Ask the Teacher',
      'personInfo' => $this->auth->getPersonDetailsForViews()
    ));
    if (isset($this->request->session["errors"])) {
      $this->response->addVars(array(
        'errors' => $this->request->session["errors"]
      ));
      unset($this->request->session["errors"]);
    }

    if (isset($this->request->session["formVars"])) {
      $this->response->addVars(array(
        'formVars' => $this->request->session["formVars"]
      ));
      unset($this->request->session["formVars"]);
    }
    $this->addVar('flash');
    return $this->response;
  }

  private function redirectToLoginPage() {
    $this->request->session['redirect'] = $this->request->server['REQUEST_URI'];
    $this->flash('You must be logged in to ask a question!', 'info');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
