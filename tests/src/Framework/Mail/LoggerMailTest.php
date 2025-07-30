<?php
namespace Tests\src\Framework\Mail;

use PHPUnit\Framework\TestCase;
use Framework\Mail\LoggerMail;
use Dotenv\Dotenv;

class LoggerMailTest extends TestCase {
  public function testSend() {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $loggerMail = new LoggerMail($_ENV['APPLICATION_LOG_FILE']);
    $expectedResult = [
      'status' => 'success',
      'message' => 'Message sent successfully',
      'aws_message_id' => 0
    ];
    $result = $loggerMail->send(
      'admin@pyangelo.com',
      'admin@pyangelo.com',
      'anyuser@hotmail.com',
      'Testing Subject',
      'Testing Body Text',
      '<html>Testing Html Text</html>',
      'UTF-8',
    );
    $this->assertSame($expectedResult, $result);
  }
}
?>
