<?php

use Framework\Presentation\Gravatar;

class GravatarTest extends \PHPUnit\Framework\TestCase
{
  public function testGetAvatarUrlWithDefaults() {
    $email = 'fred@fearless.com';
    $gravatar = new Gravatar;
    $expectedurl = 'https://www.gravatar.com/avatar/2288887234751da3eb8bcfab757b12a4?s=50&d=identicon&r=g';
    $imageTag = $gravatar->getAvatarurl($email);
    $this->assertEquals($expectedurl, $imageTag);
  }

  public function testGetAvatarUrlWithSize75() {
    $email = 'fred@fearless.com';
    $gravatar = new Gravatar(75);
    $expectedUrl = 'https://www.gravatar.com/avatar/2288887234751da3eb8bcfab757b12a4?s=75&d=identicon&r=g';
    $imageTag = $gravatar->getAvatarUrl($email);
    $this->assertEquals($expectedUrl, $imageTag);
  }

  public function testGetAvatarImageTagWithDefaults() {
    $email = 'fred@fearless.com';
    $gravatar = new Gravatar();
    $expectedImageTag = '<img src="https://www.gravatar.com/avatar/2288887234751da3eb8bcfab757b12a4?s=50&d=identicon&r=g" />';
    $imageTag = $gravatar->getAvatarImageTag($email);
    $this->assertEquals($expectedImageTag, $imageTag);
  }

  public function testGetAvatarImageTagWithSize75() {
    $email = 'fred@fearless.com';
    $gravatar = new Gravatar(75);
    $expectedImageTag = '<img src="https://www.gravatar.com/avatar/2288887234751da3eb8bcfab757b12a4?s=75&d=identicon&r=g" />';
    $imageTag = $gravatar->getAvatarImageTag($email);
    $this->assertEquals($expectedImageTag, $imageTag);
  }
}
