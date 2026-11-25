<?php
namespace Tests\src\Framework\Assets;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * mix() is kept for one release so the old views still render while the
 * first Vite deploy swaps src/ and views/.
 */
class MixHelperTest extends TestCase {
  private string $publicPath;

  protected function setUp(): void {
    $this->publicPath = sys_get_temp_dir() . '/mix-helper-test-' . bin2hex(random_bytes(4));
    mkdir($this->publicPath);
    file_put_contents(
      $this->publicPath . '/mix-manifest.json',
      json_encode(['/js/app.js' => '/js/app.js?id=abc123'])
    );
  }

  protected function tearDown(): void {
    @unlink($this->publicPath . '/mix-manifest.json');
    @rmdir($this->publicPath);
  }

  #[RunInSeparateProcess]
  public function testMixReadsTheLaravelMixManifest() {
    $_SERVER['DOCUMENT_ROOT'] = $this->publicPath;
    $this->assertSame('/js/app.js?id=abc123', mix('js/app.js'));
  }

  #[RunInSeparateProcess]
  public function testMixAndViteHelpersAreBothDefined() {
    $this->assertTrue(function_exists('mix'));
    $this->assertTrue(function_exists('vite'));
    $this->assertTrue(function_exists('viteScript'));
  }

  #[RunInSeparateProcess]
  public function testMixThrowsForAnUnknownFile() {
    $_SERVER['DOCUMENT_ROOT'] = $this->publicPath;
    $this->expectException(\Exception::class);
    mix('js/missing.js');
  }
}
