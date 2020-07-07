<?php
use PHPUnit\Framework\TestCase;
use Framework\CloudFront\CloudFront;

require(dirname(__FILE__) . '/../../../../vendor/autoload.php');

class CloudFrontTest extends TestCase {
  public function testGenerateSignedUrl() {
    $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../../../');
    $dotenv->load();

    $video = "test-video.mp4";
    $fullResource = $_ENV['CLOUDFRONT_URL_HOST'] . '/' . $video;
    $expires = time()+(60*20);
    $cloudFront = new CloudFront(
      $_ENV['AWS_CLOUDFRONT_KEY'],
      $_ENV['AWS_CLOUDFRONT_SECRET'],
      $_ENV['AWS_CLOUDFRONT_REGION'],
      $_ENV['CLOUDFRONT_PRIVATE_KEY_FILE'],
      $_ENV['CLOUDFRONT_ACCESS_KEY_ID'],
      $_ENV['CLOUDFRONT_URL_HOST']
    );
    $signedUrl = $cloudFront->generateSignedUrl (
      $video,
      $expires
    );
    $this->assertStringContainsString($fullResource . '?Policy=', $signedUrl);
    $this->assertStringContainsString('&Signature=', $signedUrl);
    $this->assertStringContainsString('&Key-Pair-Id=', $signedUrl);
  }
}
