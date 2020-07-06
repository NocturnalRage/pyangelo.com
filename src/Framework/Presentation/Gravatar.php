<?php
namespace Framework\Presentation;
use Framework\Contracts\AvatarContract;

class Gravatar implements AvatarContract {

  protected $sizeInPixels = 50;
  protected $imageSet = 'wavatar';
  protected $maxRating = 'g';

  public function __construct(
    $sizeInPixels = 50,
    $imageSet = 'identicon',
    $maxRating = 'g') {
    $this->sizeInPixels = $sizeInPixels;
    $this->imageSet = $imageSet;
    $this->maxRating = $maxRating;
  }

  public function getAvatarUrl($email) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$this->sizeInPixels&d=$this->imageSet&r=$this->maxRating";
    return $url;
  }

  public function getAvatarImageTag($email) {
    return '<img src="' . $this->getAvatarUrl($email) . '" />';
  }

  public function setSizeInPixels($sizeInPixels) {
    $this->sizeInPixels = $sizeInPixels;
  }
}
