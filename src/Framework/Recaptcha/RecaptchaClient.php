<?php
namespace Framework\Recaptcha;

use ReCaptcha\ReCaptcha;

class RecaptchaClient {
  protected $recaptcha;

  public function __construct($recaptcha) {
    $this->recaptcha = $recaptcha;
  }

  public function verified($expectedHostname, $expectedAction, $response, $ipAddress) {
    $resp = $this->recaptcha->setExpectedHostname($expectedHostname)
                            ->setExpectedAction($expectedAction)
                            ->verify($response, $ipAddress);
    return $resp->isSuccess();
  }
}
?>
