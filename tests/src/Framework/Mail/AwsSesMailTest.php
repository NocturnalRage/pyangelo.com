<?php
namespace tests\src\Framework\Mail;

use PHPUnit\Framework\TestCase;
use Framework\Mail\AwsSesMail;
use Dotenv\Dotenv;

class AwsSesMailTest extends TestCase {
  public function testSendViaSES() {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../', '.env.test');
    $dotenv->load();
    $loggerMail = new AwsSesMail(
      $_ENV['AWS_SES_KEY'],
      $_ENV['AWS_SES_SECRET'],
      $_ENV['AWS_SES_REGION']
    );
    $result = $loggerMail->send(
      'jeff@nocturnalrage.com',
      'jeff@nocturnalrage.com',
      'jeff@nocturnalrage.com',
      'Testing Subject',
      'Testing Body Text',
      '<html>Testing Html Text</html>',
      'UTF-8',
    );
    $this->assertSame('success', $result['status']);
    $this->assertSame('Message sent successfully', $result['message']);
    $this->assertGreaterThan(0, $result['aws_message_id']);
  }
}
?>
