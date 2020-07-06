<?php
namespace tests\src\PyAngelo\Controllers\Categories;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Request;
use Framework\Response;
use PyAngelo\Controllers\Categories\CategoriesOrderController;

class CategoriesOrderControllerTest extends TestCase {
  public function setUp(): void {
    $this->request = new Request($GLOBALS);
    $this->response = new Response('views');
    $this->auth = Mockery::mock('PyAngelo\Auth\Auth');
    $this->tutorialRepository = Mockery::mock('PyAngelo\Repositories\TutorialRepository');
    $this->controller = new CategoriesOrderController (
      $this->request,
      $this->response,
      $this->auth,
      $this->tutorialRepository
    );
  }
  public function tearDown(): void {
    Mockery::close();
  }

  public function testClassCanBeInstantiated() {
    $this->assertSame(get_class($this->controller), 'PyAngelo\Controllers\Categories\CategoriesOrderController');
  }

  public function testWhenNotAdmin() {
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(false);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'categories/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'You are not authorised!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAdminWithNoData() {
    $this->request->post = [];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'categories/order.json.php';
    $expectedStatus = 'error';
    $expectedMessage = 'The order of the tutorials was not received!';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }

  public function testWhenAdminWithValidData() {
    $this->request->post['idsInOrder'] = ['tutorial-1', 'tutorial-2'];
    $this->auth->shouldReceive('isAdmin')->once()->with()->andReturn(true);
    $this->tutorialRepository->shouldReceive('updateTutorialOrder')
      ->once()
      ->with('tutorial-1', 1)
      ->andReturn(1);
    $this->tutorialRepository->shouldReceive('updateTutorialOrder')
      ->once()
      ->with('tutorial-2', 2)
      ->andReturn(1);

    $response = $this->controller->exec();
    $responseVars = $response->getVars();
    $expectedViewName = 'categories/order.json.php';
    $expectedStatus = 'success';
    $expectedMessage = 'The new order has been saved.';
    $this->assertSame($expectedViewName, $response->getView());
    $this->assertSame($expectedStatus, $responseVars['status']);
    $this->assertSame($expectedMessage, $responseVars['message']);
  }
}
?>
