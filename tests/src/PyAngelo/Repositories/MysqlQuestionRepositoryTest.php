<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlQuestionRepository;
use Tests\Factory\TestData;

class MysqlQuestionRepositoryTest extends TestCase {
  protected $dbh;
  protected $stripeRepository;
  protected $testData;

  public function setUp(): void {
    $dotenv  = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $this->dbh = new \Mysqli(
      $_ENV['DB_HOST'],
      $_ENV['DB_USERNAME'],
      $_ENV['DB_PASSWORD'],
      $_ENV['DB_DATABASE']
    );
    $this->dbh->begin_transaction();
    $this->questionRepository = new MysqlQuestionRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testQuestionRepository() {
    $personId = 1;
    $questionTypeId = 1;
    $this->testData->createCountry('US', 'United States', 'USD');
    $this->testData->createPerson($personId, 'coder@hotmail.com');
    $this->testData->createQuestionType($questionTypeId);
    $formData = [
      'person_id' => $personId,
      'question_title' => 'A Test Question',
      'question' => 'What is the meaning of life?',
      'answer' => '42',
      'question_type_id' => $questionTypeId,
      'teacher_id' => $personId,
      'answered_at' => date('Y-m-d H:i:s'),
      'slug' => 'a-test-question'
    ];
    $questionId = $this->questionRepository->createQuestion(
      $formData['person_id'],
      $formData['question_title'],
      $formData['question'],
      $formData['slug']
    );
    $question = $this->questionRepository->getQuestionBySlug($formData['slug']);
    $this->assertEquals($formData['person_id'], $question['person_id']);
    $this->assertEquals($formData['question_title'], $question['question_title']);
    $this->assertEquals($formData['question'], $question['question']);
    $this->assertEquals($formData['slug'], $question['slug']);

    $unansweredQuestions = $this->questionRepository->getUnansweredQuestions();
    $this->assertEquals($formData['question_title'], $unansweredQuestions[0]['question_title']);
    $this->assertEquals($formData['question'], $unansweredQuestions[0]['question']);

    $myQuestions = $this->questionRepository->getQuestionsByPersonId($personId);
    $this->assertEquals($formData['question_title'], $myQuestions[0]['question_title']);
    $checkQuestion = $this->questionRepository->getQuestionById($myQuestions[0]['question_id']);
    $this->assertEquals($formData['question_title'], $checkQuestion['question_title']);
    $checkQuestion = $this->questionRepository->getQuestionBySlug($myQuestions[0]['slug']);
    $this->assertEquals($formData['question_title'], $checkQuestion['question_title']);
    $checkQuestion = $this->questionRepository->getQuestionBySlugWithStatus($myQuestions[0]['slug'], $personId);
    $this->assertEquals(0, $checkQuestion['favourited']);
    $rowsUpdated = $this->questionRepository->answerQuestion(
      $questionId,
      $formData['question_title'],
      $formData['question'],
      $formData['answer'],
      $formData['question_type_id'],
      $formData['teacher_id'],
      $formData['slug'],
      $formData['answered_at']
    );
    $this->assertEquals(1, $rowsUpdated);
    $publishedQuestions = $this->questionRepository->getLatestQuestions(0, 1);
    $this->assertEquals($formData['question_title'], $publishedQuestions[0]['question_title']);
    $rowsInserted = $this->questionRepository->addToQuestionAlert($questionId, $personId);
    $this->assertEquals(1, $rowsInserted);
    $alert = $this->questionRepository->shouldUserReceiveAlert($questionId, $personId);
    $this->assertEquals($questionId, $alert['question_id']);
    $followers = $this->questionRepository->getFollowers($questionId);
    $this->assertEquals($personId, $followers[0]['person_id']);

    $rowsDeleted = $this->questionRepository->removeFromQuestionAlert($questionId, $personId);
    $this->assertEquals(1, $rowsDeleted);
    $alert = $this->questionRepository->shouldUserReceiveAlert($questionId, $personId);
    $this->assertEmpty($alert);

    $questionTypes = $this->questionRepository->getAllQuestionTypes();
    $category = $this->questionRepository->getCategoryBySlug($questionTypes[0]['category_slug']);
    $this->assertEquals($formData['question_type_id'], $category['question_type_id']);
    $categoryQuestions = $this->questionRepository->getCategoryQuestionsBySlug($questionTypes[0]['category_slug']);
    $this->assertEquals($formData['question_title'], $categoryQuestions[0]['question_title']);

    $formData2 = [
      'person_id' => $personId,
      'question_title' => 'Another Test Question',
      'question' => 'What is the meaning of life?',
      'slug' => 'meaning-of-life'
    ];
    $question2Id = $this->questionRepository->createQuestion(
      $formData2['person_id'],
      $formData2['question_title'],
      $formData2['question'],
      $formData2['slug']
    );
    $rowsDeleted = $this->questionRepository->deleteQuestion($formData2['slug']);
    $this->assertEquals(1, $rowsDeleted);
  }
}

