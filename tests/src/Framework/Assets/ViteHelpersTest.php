<?php
namespace Tests\src\Framework\Assets;

use PHPUnit\Framework\TestCase;

/**
 * The template helpers in src/Helpers/functions.php. Templates load built
 * files with vite() and viteScript(); the laravel-mix helper mix() is gone.
 */
class ViteHelpersTest extends TestCase {
  public function testTheViteHelpersAreDefined() {
    $this->assertTrue(function_exists('viteManifest'));
    $this->assertTrue(function_exists('vite'));
    $this->assertTrue(function_exists('viteScript'));
  }

  public function testTheLaravelMixHelperIsGone() {
    $this->assertFalse(function_exists('mix'));
  }
}
