<?php

use Framework\Presentation\HtmlPurifierPurify;

class HtmlPurifierPurifyTest extends \PHPUnit\Framework\TestCase
{
  public function testPurify()
  {
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('HTML.Nofollow', true);
    $config->set('URI.Host', 'www.pyangelo.com');
    $htmlPurifier = new \HTMLPurifier($config);
    $purifier = new HtmlPurifierPurify($htmlPurifier);

    $html = "<h3>Some HTML Stays</h3><script>Some goes</script>";
    $expect = "<h3>Some HTML Stays</h3>";
    $actual = $purifier->purify($html);
    $this->assertSame($expect, $actual);

    $html = '<a href="http://www.ittf.com">Visit another site</a>';
    $expect = '<a href="http://www.ittf.com" rel="nofollow">Visit another site</a>';
    $actual = $purifier->purify($html);
    $this->assertSame($expect, $actual);

    $html = '<a href="https://www.pyangelo.com/coding/">Coding Lessons</a>';
    $actual = $purifier->purify($html);
    $this->assertSame($html, $actual);
  }
}
