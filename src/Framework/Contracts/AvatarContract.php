<?php
namespace Framework\Contracts;

interface AvatarContract {
  public function getAvatarUrl($email);
  public function getAvatarImageTag($email);
}
