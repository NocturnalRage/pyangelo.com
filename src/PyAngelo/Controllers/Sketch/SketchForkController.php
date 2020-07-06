<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;

class SketchForkController extends Controller {
  protected $sketchRepository;

  public function __construct(
    Request $request,
    Response $response,
    Auth $auth,
    SketchRepository $sketchRepository,
    SketchFiles $sketchFiles
  ) {
    parent::__construct($request, $response, $auth);
    $this->sketchRepository = $sketchRepository;
    $this->sketchFiles = $sketchFiles;
  }

  public function exec() {

    if (! $this->auth->loggedIn())
      return $this->redirectToLoginPage();

    if (!$this->auth->crsfTokenIsValid())
      return $this->redirectToHomePage();

    if (!isset($this->request->post['sketchId']))
      return $this->redirectToHomePage();

    $sketchId = $this->sketchRepository->forkSketch(
      $this->request->post['sketchId'],
      $this->auth->personId(),
      $this->selectRandomTitle()
    );

    if (!$sketchId)
      return $this->redirectToSketchPage();

    $sketchFiles = $this->sketchRepository->getSketchFiles($sketchId);
    $this->sketchFiles->forkSketch(
      $this->request->post['sketchId'],
      $sketchId,
      $sketchFiles
    );

    $header = "Location: /sketch/" . $sketchId;
    $this->response->header($header);
    return $this->response;
  }

  private function selectRandomTitle() {
    $word1 = [
      'red',
      'green',
      'blue',
      'yellow',
      'purple',
      'black',
      'white',
      'pink',
      'ivory',
      'pearl',
      'coconut',
      'tan',
      'beige',
      'fawn',
      'granola',
      'sand',
      'shortbread',
      'hazelnut',
      'latte',
      'canary',
      'gold',
      'butter',
      'mustard',
      'corn',
      'pineapple',
      'honey',
      'blonde',
      'orange',
      'tangerine',
      'merigold',
      'cider',
      'rust',
      'ginger',
      'tiger',
      'fire',
      'bronze',
      'apricot',
      'clay',
      'carrot',
      'spice',
      'cherry',
      'rose',
      'jam',
      'ruby',
      'apple',
      'berry',
      'lipstick',
      'blush',
      'scarlet',
      'wine',
      'blood',
      'punch',
      'salmon',
      'coral',
      'peach',
      'strawberry',
      'magenta',
      'bubblegum',
      'plum',
      'violet',
      'lilac',
      'gravy',
      'toast',
      'cereal',
      'ice',
      'cream',
      'flavour',
      'cool',
      'fun',
      'best',
      'greatest',
      'random',
      'breezy',
      'sneezy',
      'tropical',
      'laughable',
      'sporty',
      'interesting'
    ];
    $word2 = [
      'train',
      'car',
      'bus',
      'truck',
      'caravan',
      'tree',
      'road',
      'band',
      'science',
      'computer',
      'friend',
      'enemy',
      'foe',
      'cat',
      'dog',
      'frog',
      'lamb',
      'cow',
      'bee',
      'wasp',
      'ant',
      'farm',
      'lion',
      'bear',
      'giraffe',
      'bench',
      'table',
      'random',
      'leader',
      'student',
      'statement',
      'sketch',
      'program',
      'tadpole',
      'feline',
      'mouse',
      'computer',
      'screen',
      'pen',
      'pencil',
      'school',
      'university',
      'subject',
      'maths',
      'language',
      'python',
      'bat',
      'believer',
      'storm',
      'clouds',
      'thunder',
      'lightning'
    ];
    return $word1[array_rand($word1)] . '-' . $word2[array_rand($word2)];
  }

  private function redirectToLoginPage() {
    $this->flash("You must be logged in to fork a sketch", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToHomePage() {
    $this->flash("Sorry, we could not fork the sketch.", "danger");
    $this->response->header('Location: /');
    return $this->response;
  }

  private function redirectToSketchPage() {
    $this->flash('Error! We could not fork the sketch for you :(', 'danger');
    $this->response->header('Location: /sketch/' . $this->request->post['sketchId']);
    return $this->response;
  }
}
