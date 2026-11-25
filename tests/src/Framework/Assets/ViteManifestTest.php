<?php
namespace Tests\src\Framework\Assets;

use PHPUnit\Framework\TestCase;
use Framework\Assets\ViteManifest;

class ViteManifestTest extends TestCase {
  private string $publicPath;

  protected function setUp(): void {
    $this->publicPath = sys_get_temp_dir() . '/vite-manifest-test-' . bin2hex(random_bytes(4));
    mkdir($this->publicPath . '/build/.vite', 0777, true);
    $this->writeManifest($this->publicPath . '/build/.vite/manifest.json', [
      'resources/assets/sass/app.scss' => [
        'file' => 'assets/sass/app-AAA.css',
        'src' => 'resources/assets/sass/app.scss',
        'isEntry' => true
      ],
      'resources/assets/js/app.js' => [
        'file' => 'assets/js/app.js-BBB.js',
        'isEntry' => true,
        'imports' => ['_jquery-CCC.js']
      ],
      'resources/assets/js/playground.js' => [
        'file' => 'assets/js/playground.js-DDD.js',
        'isEntry' => true,
        'imports' => ['_EditorSetup-EEE.js', '_jquery-CCC.js']
      ],
      '_EditorSetup-EEE.js' => [
        'file' => 'assets/EditorSetup-EEE.js',
        'imports' => ['_pyangelo-notify-FFF.js', '_jquery-CCC.js']
      ],
      '_pyangelo-notify-FFF.js' => [
        'file' => 'assets/pyangelo-notify-FFF.js',
        'css' => ['assets/pyangelo-notify-GGG.css']
      ],
      '_jquery-CCC.js' => [
        'file' => 'assets/jquery-CCC.js'
      ],
      'resources/assets/js/quote"s.js' => [
        'file' => 'assets/js/quote"s.js',
        'isEntry' => true
      ]
    ]);
  }

  protected function tearDown(): void {
    foreach ([
      '/build/.vite/manifest.json', '/build/manifest.json'
    ] as $file) {
      @unlink($this->publicPath . $file);
    }
    @rmdir($this->publicPath . '/build/.vite');
    @rmdir($this->publicPath . '/build');
    @rmdir($this->publicPath);
  }

  private function writeManifest(string $path, array $chunks): void {
    file_put_contents($path, json_encode($chunks));
  }

  public function testUrlOfStylesheetEntry() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->assertSame('/build/assets/sass/app-AAA.css', $manifest->url('resources/assets/sass/app.scss'));
  }

  public function testUrlAcceptsLeadingSlash() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->assertSame('/build/assets/js/app.js-BBB.js', $manifest->url('/resources/assets/js/app.js'));
  }

  public function testScriptTagsForEntryWithOneImport() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $expected =
      '<link rel="modulepreload" href="/build/assets/jquery-CCC.js">' . "\n" .
      '<script type="module" src="/build/assets/js/app.js-BBB.js"></script>';
    $this->assertSame($expected, $manifest->scriptTags('resources/assets/js/app.js'));
  }

  public function testScriptTagsLinkCssFromNestedImportsAndPreloadEachChunkOnce() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $expected =
      '<link rel="stylesheet" href="/build/assets/pyangelo-notify-GGG.css">' . "\n" .
      '<link rel="modulepreload" href="/build/assets/EditorSetup-EEE.js">' . "\n" .
      '<link rel="modulepreload" href="/build/assets/pyangelo-notify-FFF.js">' . "\n" .
      '<link rel="modulepreload" href="/build/assets/jquery-CCC.js">' . "\n" .
      '<script type="module" src="/build/assets/js/playground.js-DDD.js"></script>';
    $this->assertSame($expected, $manifest->scriptTags('resources/assets/js/playground.js'));
  }

  public function testScriptTagsEscapeAttributes() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->assertSame(
      '<script type="module" src="/build/assets/js/quote&quot;s.js"></script>',
      $manifest->scriptTags('resources/assets/js/quote"s.js')
    );
  }

  public function testCustomBase() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath, '/assets-cdn/');
    $this->assertSame('/assets-cdn/assets/js/app.js-BBB.js', $manifest->url('resources/assets/js/app.js'));
  }

  public function testMissingEntryThrows() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Unable to locate Vite entry: resources/assets/js/missing.js');
    $manifest->url('resources/assets/js/missing.js');
  }

  public function testSharedChunkIsNotAnEntry() {
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->expectException(\RuntimeException::class);
    $manifest->scriptTags('_jquery-CCC.js');
  }

  public function testMissingManifestThrows() {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('The Vite manifest does not exist');
    new ViteManifest($this->publicPath . '/build/nope.json');
  }

  public function testInvalidManifestThrows() {
    file_put_contents($this->publicPath . '/build/manifest.json', 'not json');
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('not valid JSON');
    new ViteManifest($this->publicPath . '/build/manifest.json');
  }

  public function testFallsBackToOlderManifestLocation() {
    unlink($this->publicPath . '/build/.vite/manifest.json');
    $this->writeManifest($this->publicPath . '/build/manifest.json', [
      'resources/assets/js/app.js' => ['file' => 'assets/old-app.js', 'isEntry' => true]
    ]);
    $manifest = ViteManifest::fromPublicPath($this->publicPath);
    $this->assertSame('/build/assets/old-app.js', $manifest->url('resources/assets/js/app.js'));
  }
}
