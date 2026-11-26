<?php
namespace Tests\src\Framework\CloudFront;

use PHPUnit\Framework\TestCase;
use Framework\CloudFront\CloudFront;

/**
 * Signing a CloudFront URL happens locally with the key pair's private key,
 * so the test makes its own key pair: it needs no AWS credentials and checks
 * the signature really verifies, not just that the URL has the right shape.
 */
class CloudFrontTest extends TestCase {
  private const HOST = 'https://example-distribution.cloudfront.net';
  private const KEY_PAIR_ID = 'KTESTKEYPAIRID';

  private string $keyFile;
  private string $publicKey;

  protected function setUp(): void {
    $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
    openssl_pkey_export($key, $privatePem);
    $this->publicKey = openssl_pkey_get_details($key)['key'];
    $this->keyFile = tempnam(sys_get_temp_dir(), 'cloudfront-test-');
    file_put_contents($this->keyFile, $privatePem);
  }

  protected function tearDown(): void {
    @unlink($this->keyFile);
  }

  private function cloudFront(?string $keyFile = null): CloudFront {
    return new CloudFront(
      'test-access-key',
      'test-secret',
      'us-east-1',
      $keyFile ?? $this->keyFile,
      self::KEY_PAIR_ID,
      self::HOST
    );
  }

  /** CloudFront's URL-safe base64: + = / become - _ ~ */
  private function decode(string $value): string {
    return base64_decode(strtr($value, '-_~', '+=/'));
  }

  public function testGenerateSignedUrl() {
    $expires = time() + 60 * 20;
    $signedUrl = $this->cloudFront()->generateSignedUrl('videos/test-video.mp4', $expires);

    $resource = self::HOST . '/videos/test-video.mp4';
    $this->assertStringStartsWith($resource . '?', $signedUrl);
    parse_str(parse_url($signedUrl, PHP_URL_QUERY), $query);
    $this->assertSame(['Policy', 'Signature', 'Key-Pair-Id'], array_keys($query));
    $this->assertSame(self::KEY_PAIR_ID, $query['Key-Pair-Id']);

    // The policy grants this resource until the expiry time.
    $policy = $this->decode($query['Policy']);
    $statement = json_decode($policy, true)['Statement'][0];
    $this->assertSame($resource, $statement['Resource']);
    $this->assertSame($expires, $statement['Condition']['DateLessThan']['AWS:EpochTime']);

    // CloudFront checks an RSA-SHA1 signature of the policy with the public key.
    $signature = $this->decode($query['Signature']);
    $this->assertSame(1, openssl_verify($policy, $signature, $this->publicKey, OPENSSL_ALGO_SHA1));
  }

  public function testSignatureDoesNotVerifyWithADifferentKey() {
    $signedUrl = $this->cloudFront()->generateSignedUrl('videos/test-video.mp4', time() + 60);
    parse_str(parse_url($signedUrl, PHP_URL_QUERY), $query);
    $otherKey = openssl_pkey_get_details(openssl_pkey_new([
      'private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA
    ]))['key'];
    $this->assertSame(0, openssl_verify(
      $this->decode($query['Policy']), $this->decode($query['Signature']), $otherKey, OPENSSL_ALGO_SHA1
    ));
  }

  public function testMissingKeyFileIsReported() {
    $this->expectException(\InvalidArgumentException::class);
    $this->cloudFront(sys_get_temp_dir() . '/no-such-cloudfront-key.pem')
      ->generateSignedUrl('videos/test-video.mp4', time() + 60);
  }
}
