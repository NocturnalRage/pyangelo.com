<?php
namespace Framework\CloudFront;

use Aws\CloudFront\CloudFrontClient;

class CloudFront {
  protected $client;
  protected $private_key_file;
  protected $access_key_id;
  protected $streamHostUrl;

  public function __construct(
    $aws_cloudfront_key,
    $aws_cloudfront_secret,
    $aws_cloudfront_region,
    $private_key_file,
    $access_key_id,
    $streamHostUrl) {
    $this->client = CloudFrontClient::factory(array(
      'credentials' => array(
        'key'    => $aws_cloudfront_key,
        'secret' => $aws_cloudfront_secret,
      ),
      'version' => 'latest',
      'region'  => $aws_cloudfront_region
    ));
    $this->private_key_file = $private_key_file;
    $this->access_key_id = $access_key_id;
    $this->streamHostUrl = $streamHostUrl;
  }

  public function generateSignedUrl($resourceKey, $expires) {
    $fullResource = $this->streamHostUrl . '/' . $resourceKey;

$customPolicy =  <<<POLICY
{
  "Statement": [
    {
      "Resource": "{$fullResource}",
      "Condition": {
        "DateLessThan": {"AWS:EpochTime": {$expires}}
      }
    }
  ]
}
POLICY;

    $signedUrlCustomPolicy = $this->client->getSignedUrl([
      'url' => $fullResource,
      'policy' => $customPolicy,
      'private_key' => $this->private_key_file,
      'key_pair_id' => $this->access_key_id
    ]);

    return $signedUrlCustomPolicy;
  }
}
?>
