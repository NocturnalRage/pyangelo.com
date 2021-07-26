<?php
namespace tests\src\Framework\Mail;

use PHPUnit\Framework\TestCase;
use Mockery;
use Framework\Mail\Campaigns;
use IvoPetkov\HTML5DOMDocument;

class CampaignsTest extends TestCase {
  public function tearDown(): void {
    Mockery::close();
  }

  public function testSendOutstanding() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../..', '.env.test');
    $dotenv->load();
    $awsMessageId = 'test-aws-message-id';
    $sendEmailResult = [
      'status' =>'success',
      'aws_message_id' => $awsMessageId
    ];
    $campaignId = 1;
    $segmentId = 1;
    $segmentName = 'All members';
    $listId = 1;
    $autoresponderWhereCondition = '1=1';
    $fromEmail = 'admin@nocturnalrage.com';
    $subject = 'Hello [givenname]';
    $expectedSubject = 'Hello Fast';
    $bodyHtml = <<<ENDHTML
<!DOCTYPE html>
<html>
        <head>
        </head>
        <body>
          [showinbrowser]
          <p>Hello [givenname],</p>
          <a href="https://www.pyangelo.com">Click here to learn about coding</a>
          <a href="https://www.github.com">Click here to learn about Github</a>
          <p>[unsubscribe]</p>
        </body>
      </html>
ENDHTML;
    $expectedBodyHtml = <<<ENDHTML
<!DOCTYPE html>
<html>
        <head>
        </head>
        <body>
          <a href="https://www.pyangelo.com/campaign/display/1IOAAL/1D132X/4f2efae936aac9e2315e3fca516143e3">View in your browser</a>
          <p>Hello Fast,</p>
          <a href="https://www.pyangelo.com/campaign/links/1IOAAL/1D132X/UI4JW">Click here to learn about coding</a>
          <a href="https://www.pyangelo.com/campaign/links/1IOAAL/1D132X/UI4JZ">Click here to learn about Github</a>
          <p><a href="https://www.pyangelo.com/campaign/unsubscribe/1IOAAL/1D132X/4f2efae936aac9e2315e3fca516143e3">Unsubscribe instantly.</a></p>
        <img src="https://www.pyangelo.com/campaign/open/1IOAAL/1D132X" width="1" height="1"  border="0" /></body>
      </html>
ENDHTML;
    $bodyText = <<<ENDTEXT
        [showinbrowser]
        Hello [givenname],
        https://www.pyangelo.com Click here to learn about coding
        [unsubscribe]
ENDTEXT;
    $expectedBodyText = <<<ENDTEXT
        https://www.pyangelo.com/campaign/display/1IOAAL/1D132X/4f2efae936aac9e2315e3fca516143e3
        Hello Fast,
        https://www.pyangelo.com Click here to learn about coding
        https://www.pyangelo.com/campaign/unsubscribe/1IOAAL/1D132X/4f2efae936aac9e2315e3fca516143e3
ENDTEXT;
    $campaigns = [
      [
        'campaign_id' => $campaignId,
        'segment_id' => $segmentId,
        'segment_name' => $segmentName,
        'list_id' => $listId,
        'autoresponder_where_condition' => $autoresponderWhereCondition,
        'segment_name' => $segmentId,
        'from_email' => $fromEmail,
        'subject' => $subject,
        'body_html' => $bodyHtml,
        'body_text' => $bodyText
      ]
    ];
    $subscribers = [
      [
        'person_id' => 10,
        'given_name' => 'Slow',
        'family_name' => 'Coach',
        'email' => 'slowcoach@hotmail.com',
        'created_at' => '2017-01-01 08:00:00',
        'last_campaign_at' => '2017-01-01 10:00:00',
        'last_autoresponder_at' => '2017-01-02 10:00:00',
      ],
      [
        'person_id' => 1,
        'given_name' => 'Fast',
        'family_name' => 'Fred',
        'email' => 'fastfred@hotmail.com',
        'created_at' => '2017-01-01 08:00:00',
        'last_campaign_at' => '2017-01-01 10:00:00',
        'last_autoresponder_at' => '2017-01-02 10:00:00',
      ]
    ];
    $trackableLink = [
      'link_id' => 1,
      'href' => 'https://www.pyangelo.com'
    ];
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getCampaignsToBeSent')
      ->once()->with()->andReturn($campaigns);
    $repository->shouldReceive('getSubscribers')
      ->once()
      ->with($listId, $autoresponderWhereCondition)
      ->andReturn($subscribers);
    $repository->shouldReceive('getTrackableLink')
      ->twice()->with('https://www.pyangelo.com')->andReturn($trackableLink);
    $repository->shouldReceive('getTrackableLink')
      ->twice()->with('https://www.github.com')->andReturn(NULL);
    $repository->shouldReceive('createTrackableLink')
      ->twice()->with('https://www.github.com')->andReturn(2);
    $repository->shouldReceive('recordCampaignActivity')
      ->twice()->with(1, $campaignId, Mockery::any(), $awsMessageId);
    $repository->shouldReceive('updateLastCampaign')
      ->once()->with(10, $listId)->andReturn(1);
    $repository->shouldReceive('updateLastCampaign')
      ->once()->with(1, $listId)->andReturn(1);
    $repository->shouldReceive('updateStatus')
      ->once()->with($campaignId, 3)
      ->andReturn(1);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')
      ->twice()
      ->with(
        $fromEmail,
        $fromEmail,
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        'UTF-8'
      )
      ->andReturn($sendEmailResult);
    $dom = new HTML5DOMDocument();

    $campaignsToBeSent = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $campaignsToBeSent->sendOutstanding();

    $this->assertEquals($campaignsToBeSent->bodyHtml(), $expectedBodyHtml);
    $this->assertEquals($campaignsToBeSent->bodyText(), $expectedBodyText);
    $this->assertEquals($campaignsToBeSent->subject(), $expectedSubject);
  }

  public function testSendTestInvalidCampaign() {
    $campaignId = 100;
    $toEmail = 'anyone@example.com';

    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getCampaignById')
      ->once()->with($campaignId)->andReturn(NULL);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $status = $campaigns->sendTest($campaignId, $toEmail);
    $this->assertFalse($status);
  }

  public function testSendTestValidCampaign() {
    $campaignId = 100;
    $fromEmail = 'admin@nocturnalrage.com';
    $subject = 'Subject';
    $bodyHtml = 'Body HTML';
    $bodyText = 'Body Text';
    $campaign = [
      'campaign_id' => $campaignId,
      'from_email' => $fromEmail,
      'subject' => $subject,
      'body_html' => $bodyHtml,
      'body_text' => $bodyText
    ];
    $toEmail = 'anyone@example.com';
    $awsMessageId = 'test-aws-message-id';
    $sendEmailResult = [
      'status' =>'success',
      'aws_message_id' => $awsMessageId
    ];

    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getCampaignById')
      ->once()->with($campaignId)->andReturn($campaign);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $mailer->shouldReceive('send')
      ->once()
      ->with(
        $fromEmail,
        $fromEmail,
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        Mockery::any(),
        'UTF-8'
      )
      ->andReturn($sendEmailResult);
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $status = $campaigns->sendTest($campaignId, $toEmail);
    $this->assertTrue($status);
  }

  public function testRecordOpen() {
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('recordCampaignActivity')
      ->once()
      ->with(2, 1, 2);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $campaigns->recordOpen('1IOAAL', '1D132Y');
    $this->assertTrue(true);
  }

  public function testRecordClick() {
    $linkId = 1;
    $href = 'https://www.pyangelo.com';
    $link = [
      'link_id' => $linkId,
      'href' => $href
    ];
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getTrackableLinkById')
      ->once()
      ->with($linkId)
      ->andReturn($link);
    $repository->shouldReceive('recordCampaignActivity')
      ->once()
      ->with(5, 1, 2, NULL, $linkId);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $campaigns->recordClick('1IOAAL', '1D132Y', 'UI4JW');
    $this->assertTrue(true);
  }

  public function testUnsubscribe() {
    $personId = 2;
    $givenName = 'Fast';
    $familyName = 'Fred';
    $createdAt = '2017-01-01';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'created_at' => $createdAt
    ];
    $unsubscribeHash = md5($givenName . $familyName . $createdAt);
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getPersonById')
      ->once()->with($personId)->andReturn($person);
    $repository->shouldReceive('unsubscribeFromAllLists')
      ->once()->with($personId);
    $repository->shouldReceive('recordCampaignActivity')
      ->once()
      ->with(6, 1, $personId);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $campaigns->unsubscribe('1IOAAL', '1D132Y', $unsubscribeHash);
    $this->assertTrue(true);
  }

  public function testPrepareWebVersionInvalidCampaign() {
    $campaignId = 1;
    $personId = 2;
    $givenName = 'Fast';
    $familyName = 'Fred';
    $createdAt = '2017-01-01';
    $person = [
      'person_id' => $personId,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'created_at' => $createdAt
    ];
    $personHash = md5($givenName . $familyName . $createdAt);
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getCampaignById')
      ->once()->with($campaignId)->andReturn(NULL);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $status = $campaigns->prepareWebVersion('1IOAAL', '1D132Y', $personHash);
    $this->assertFalse($status);
  }

  public function testPrepareWebVersion() {
    $campaignId = 1;
    $fromEmail = 'admin@nocturnalrage.com';
    $subject = 'Subject';
    $bodyHtml = 'Body HTML';
    $bodyText = 'Body Text';
    $campaign = [
      'campaign_id' => $campaignId,
      'from_email' => $fromEmail,
      'subject' => $subject,
      'body_html' => $bodyHtml,
      'body_text' => $bodyText
    ];
    $personId = 2;
    $email = 'fastfred@hotmail.com';
    $givenName = 'Fast';
    $familyName = 'Fred';
    $createdAt = '2017-01-01';
    $subscriber = [
      'person_id' => $personId,
      'email' => $email,
      'given_name' => $givenName,
      'family_name' => $familyName,
      'created_at' => $createdAt
    ];
    $personHash = md5($givenName . $familyName . $createdAt);
    $repository = Mockery::mock('PyAngelo\Repositories\CampaignRepository');
    $repository->shouldReceive('getCampaignById')
      ->once()->with($campaignId)->andReturn($campaign);
    $repository->shouldReceive('getSubscriberByPersonId')
      ->once()->with($personId)->andReturn($subscriber);
    $mailer = Mockery::mock('Framework\Mail\LoggerMail');
    $dom = new HTML5DOMDocument();
    $campaigns = new Campaigns(
      $repository,
      $mailer,
      $dom,
      'https://www.pyangelo.com'
    );
    $display = $campaigns->prepareWebVersion('1IOAAL', '1D132Y', $personHash);
    $bodyHtml = <<<ENDHTML
<!DOCTYPE html>
<html>
<body>Body HTML<img src="https://www.pyangelo.com/campaign/open/1IOAAL/1D132Y" width="1" height="1"  border="0" /></body></html>
ENDHTML;
    $expectedDisplay = [
      'campaign_id' => $campaignId,
      'subject' => 'Subject',
      'body_html' => $bodyHtml
    ];
    $this->assertEquals($expectedDisplay, $display);
  }
}
