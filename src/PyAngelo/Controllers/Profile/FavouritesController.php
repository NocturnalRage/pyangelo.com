<?php
namespace PyAngelo\Controllers\Profile;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\TutorialRepository;

class FavouritesController extends Controller {
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
    if (! $this->auth->loggedIn())
      return $this->redirectToLogin();

    $favourites = $this->tutorialRepository->getAllFavourites(
      $this->auth->personId()
    );

    if (! $favourites) {
      $this->response->setView('profile/no-favourites.html.php');
      $this->response->setVars(array(
        'pageTitle' => 'My Favourites',
        'metaDescription' => 'Save all your favourite PyAngelo videos here so you can easily find them later.',
        'activeLink' => 'favourites',
        'personInfo' => $this->auth->getPersonDetailsForViews()
      ));
      return $this->response;
    }

    $this->response->setView('profile/favourites.html.php');
    $this->response->setVars(array(
      'pageTitle' => 'My Favourites',
      'metaDescription' => 'Save all your favourite PyAngelo videos here so you can easily find them later.',
      'activeLink' => 'favourites',
      'personInfo' => $this->auth->getPersonDetailsForViews(),
      'favourites' => $favourites
    ));
    return $this->response;
  }

  private function redirectToLogin() {
    $this->flash('You must be logged in to view your favourites.', 'info');
    $this->response->header('Location: /login');
    return $this->response;
  }
}
