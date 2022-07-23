<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlQuizRepository;
use Tests\Factory\TestData;

class MysqlQuizRepositoryTest extends TestCase {
  protected $dbh;
  protected $quizRepository;
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
    $this->quizRepository = new MysqlQuizRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testGetSkillBySlug() {
    $skillName = 'Variables';
    $slug = 'variables';
    $this->testData->createSkill($skillName, $slug);

    $skill = $this->quizRepository->getSkillBySlug($slug);
    $this->assertEquals($skillName, $skill['skill_name']);
  }
}
