<?php
namespace Tests\PyAngelo\Repositories;

use PHPUnit\Framework\TestCase;
use PyAngelo\Repositories\MysqlClassRepository;
use Tests\Factory\TestData;

class MysqlClassRepositoryTest extends TestCase {
  protected $dbh;
  protected $classRepository;
  protected $testData;
  protected $personId;

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
    $this->classRepository = new MysqlClassRepository($this->dbh);
    $this->testData = new TestData($this->dbh);
    $this->personId = 101;
    $this->testData->createCountry('US', 'United States', 'USD');
    $this->testData->createPerson($this->personId, 'admin@pyangelo.com');
  }

  public function tearDown(): void {
    $this->dbh->rollback();
    $this->dbh->close();
  }

  public function testCreateNewClass() {
    $className = 'Game Development';
    $classCode = 'CLASS-CODE';
    $classId = $this->classRepository->createNewClass(
      $this->personId, $className, $classCode
    );
    $this->assertGreaterThan(0, $classId);

    $class = $this->classRepository->getClassById($classId);
    $this->assertEquals($className, $class['class_name']);
    $this->assertEquals($classCode, $class['class_code']);

    $class = $this->classRepository->getClassByCode($classCode);
    $this->assertEquals($className, $class['class_name']);
    $this->assertEquals($classCode, $class['class_code']);

    $classes = $this->classRepository->getTeacherClasses($this->personId);
    $this->assertCount(1, $classes);
    $this->assertEquals($className, $classes[0]['class_name']);

    $newClassName = 'Coding 101';
    $rowsUpdated = $this->classRepository->updateClass(
      $classId, $newClassName
    );
    $this->assertEquals(1, $rowsUpdated);
    $class = $this->classRepository->getClassById($classId);
    $this->assertEquals($newClassName, $class['class_name']);
    $this->assertEquals($classCode, $class['class_code']);

    $this->classRepository->archiveClass($classId);
    $class = $this->classRepository->getClassById($classId);
    $this->assertEquals(1, $class['archived']);

    $this->classRepository->restoreClass($classId);
    $class = $this->classRepository->getClassById($classId);
    $this->assertEquals(0, $class['archived']);
  }

  public function testGetStudentClasses() {
    $studentId = 202;
    $this->testData->createPerson($studentId, 'student@hotmail.com');

    $className = 'Game Development';
    $classCode = 'CLASS-CODE';
    $classId = $this->classRepository->createNewClass(
      $this->personId, $className, $classCode
    );
    $rowsInserted = $this->classRepository->joinClass($classId, $studentId);
    $this->assertEquals(1, $rowsInserted);

    $classes = $this->classRepository->getStudentClasses($studentId);
    $this->assertCount(1, $classes);
    $this->assertEquals($className, $classes[0]['class_name']);

    $students = $this->classRepository->getClassStudents($classId);
    $this->assertCount(1, $students);
    $this->assertEquals($studentId, $students[0]['person_id']);

    $student = $this->classRepository->getStudentFromClass(
      $classId, $studentId
    );
    $this->assertEquals($studentId, $student['person_id']);
    $this->assertEquals('student@hotmail.com', $student['email']);
  }

}
