<?php
namespace PyAngelo\Controllers\Sketch;

use Framework\{Request, Response};
use PyAngelo\Auth\Auth;
use PyAngelo\Controllers\Controller;
use PyAngelo\Repositories\SketchRepository;
use PyAngelo\Utilities\SketchFiles;

class SketchCreateController extends Controller {
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

    if (! $this->auth->crsfTokenIsValid()) {
      $this->flash('Please create sketches from the PyAngelo website!', 'danger');
      $this->response->header('Location: /sketch');
      return $this->response;
    }

    if (!isset($this->request->post['collectionId'])) {
      $collectionId = null;
    }
    elseif (!($collection = $this->sketchRepository->getCollectionById(
      $this->request->post['collectionId']
    ))) {
      $collectionId = null;
    }
    elseif ($collection['person_id'] != $this->auth->personId()) {
      $collectionId = null;
    }
    else {
      $collectionId = $this->request->post['collectionId'];
    }

    $sketchId = $this->sketchRepository->createNewSketch(
      $this->auth->personId(),
      $this->selectRandomTitle(),
      $collectionId
    );

    if (!$sketchId)
      return $this->redirectToSketchPage();

    $this->sketchFiles->createNewMain($this->auth->personId(), $sketchId);

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
    $this->flash("You must be logged in to create a new sketch", "danger");
    $this->response->header('Location: /login');
    return $this->response;
  }

  private function redirectToSketchPage() {
    $this->flash('Error! We could not create a new sketch for you :(', 'danger');
    $this->response->header('Location: /sketch');
    return $this->response;
  }
}
