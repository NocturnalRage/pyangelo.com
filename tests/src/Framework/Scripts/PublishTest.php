<?php
namespace Tests\src\Framework\Scripts;

use PHPUnit\Framework\TestCase;

/**
 * Runs src/Framework/Scripts/publish.sh against temporary directories laid
 * out like the git checkout (GITDIR) and the live site (APPHOME).
 */
class PublishTest extends TestCase {
  private string $root;
  private string $git;
  private string $app;

  protected function setUp(): void {
    $this->root = sys_get_temp_dir() . '/publish-test-' . bin2hex(random_bytes(4));
    $this->git = $this->root . '/git';
    $this->app = $this->root . '/app';

    $this->write($this->git, [
      'config/app.php' => 'new config',
      'public/build/assets/js/app.js-NEW.js' => 'new app',
      'public/build/.vite/manifest.json' => '{"release":"new"}',
      'public/images/new.png' => 'new image',
      'public/samples/new.wav' => 'new sample',
      'public/index.php' => 'new front controller',
      'src/New.php' => 'new src',
      'views/new.html.php' => 'new view'
    ]);
    $this->write($this->app, [
      'config/app.php' => 'old config',
      'public/build/assets/js/app.js-OLD.js' => 'previous app',
      'public/build/assets/js/stale-OLD.js' => 'app from an old release',
      'public/build/.vite/manifest.json' => '{"release":"old"}',
      'public/images/old.png' => 'old image',
      'public/samples/old.wav' => 'old sample',
      'public/index.php' => 'old front controller',
      'public/mix-manifest.json' => '{}',
      'public/js/app.js' => 'laravel-mix app',
      'src/Old.php' => 'old src',
      'views/old.html.php' => 'old view'
    ]);
    touch($this->app . '/public/build/assets/js/stale-OLD.js', strtotime('-10 days'));
    touch($this->app . '/public/build/assets/js/app.js-OLD.js', strtotime('-2 days'));
  }

  protected function tearDown(): void {
    exec('rm -rf ' . escapeshellarg($this->root));
  }

  private function write(string $base, array $files): void {
    foreach ($files as $path => $contents) {
      $full = $base . '/' . $path;
      if (! is_dir(dirname($full))) {
        mkdir(dirname($full), 0777, true);
      }
      file_put_contents($full, $contents);
    }
  }

  private function publish(?string $git = null, ?string $app = null, string $env = ''): array {
    $script = dirname(__DIR__, 4) . '/src/Framework/Scripts/publish.sh';
    exec(
      $env . 'bash ' . escapeshellarg($script) . ' ' .
      escapeshellarg($git ?? $this->git) . ' ' . escapeshellarg($app ?? $this->app) . ' 2>&1',
      $output,
      $status
    );
    return [$status, implode("\n", $output)];
  }

  private function leftovers(): array {
    $found = [];
    foreach (['', '/public', '/public/build/.vite'] as $dir) {
      foreach (glob($this->app . $dir . '/*.{new,old}', GLOB_BRACE) as $path) {
        $found[] = $path;
      }
    }
    return $found;
  }

  public function testReplacesTheReleaseFiles() {
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);

    $this->assertStringEqualsFile($this->app . '/config/app.php', 'new config');
    $this->assertStringEqualsFile($this->app . '/public/index.php', 'new front controller');
    $this->assertFileExists($this->app . '/src/New.php');
    $this->assertFileDoesNotExist($this->app . '/src/Old.php');
    $this->assertFileExists($this->app . '/views/new.html.php');
    $this->assertFileDoesNotExist($this->app . '/views/old.html.php');
    $this->assertFileExists($this->app . '/public/images/new.png');
    $this->assertFileDoesNotExist($this->app . '/public/images/old.png');
    $this->assertFileExists($this->app . '/public/samples/new.wav');
    $this->assertFileDoesNotExist($this->app . '/public/samples/old.wav');
    $this->assertSame([], $this->leftovers());
  }

  public function testSwitchesTheManifestAndKeepsRecentBuildFiles() {
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);

    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"new"}');
    $this->assertFileExists($this->app . '/public/build/assets/js/app.js-NEW.js');
    // Pages opened before the deploy may still ask for the previous files.
    $this->assertFileExists($this->app . '/public/build/assets/js/app.js-OLD.js');
    // Files no build has included for over a week are removed.
    $this->assertFileDoesNotExist($this->app . '/public/build/assets/js/stale-OLD.js');
  }

  public function testNeverRemovesFilesFromTheBuildBeingPublished() {
    // A file in this build can be old on disk if nothing changed it; cp gives
    // it the current time on the live site, so pruning keeps it.
    touch($this->git . '/public/build/assets/js/app.js-NEW.js', strtotime('-30 days'));
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileExists($this->app . '/public/build/assets/js/app.js-NEW.js');
  }

  public function testLeavesTheLaravelMixFilesForTheFirstViteDeploy() {
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileExists($this->app . '/public/mix-manifest.json');
    $this->assertFileExists($this->app . '/public/js/app.js');
  }

  public function testFirstViteDeployWhenThereIsNoBuildDirectoryYet() {
    exec('rm -rf ' . escapeshellarg($this->app . '/public/build'));
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"new"}');
    $this->assertFileExists($this->app . '/public/build/assets/js/app.js-NEW.js');
  }

  public function testRecoversFromAnInterruptedPublish() {
    // A previous run stopped after staging: the .new and .old copies remain.
    $this->write($this->app, [
      'src.new/Partial.php' => 'half copied',
      'views.old/old.html.php' => 'left over'
    ]);
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileDoesNotExist($this->app . '/src/Partial.php');
    $this->assertFileExists($this->app . '/src/New.php');
    $this->assertSame([], $this->leftovers());
  }

  public function testFallsBackToTwoRenamesWhenMvCannotExchange() {
    // An mv without --exchange, like coreutils before 9.5.
    $bin = $this->root . '/bin';
    mkdir($bin);
    $realMv = trim(shell_exec('command -v mv'));
    file_put_contents($bin . '/mv', "#!/usr/bin/env bash\n" .
      "for arg in \"\$@\"; do [ \"\$arg\" = --exchange ] && { echo 'mv: unrecognized option' >&2; exit 1; }; done\n" .
      "exec " . escapeshellarg($realMv) . " \"\$@\"\n");
    chmod($bin . '/mv', 0755);

    [$status, $output] = $this->publish(null, null, 'PATH=' . escapeshellarg($bin . ':' . getenv('PATH')) . ' ');
    $this->assertSame(0, $status, $output);
    $this->assertFileExists($this->app . '/src/New.php');
    $this->assertFileDoesNotExist($this->app . '/src/Old.php');
    $this->assertFileExists($this->app . '/views/new.html.php');
    $this->assertSame([], $this->leftovers());
  }

  public function testStopsWithoutChangingAnythingWhenADirectoryIsMissing() {
    [$status] = $this->publish($this->root . '/missing', $this->app);
    $this->assertSame(1, $status);
    $this->assertFileExists($this->app . '/src/Old.php');
  }

  public function testStopsBeforeTheSwapIfTheBuildIsMissing() {
    exec('rm -rf ' . escapeshellarg($this->git . '/public/build/.vite'));
    [$status] = $this->publish();
    $this->assertNotSame(0, $status);
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"old"}');
    $this->assertFileExists($this->app . '/src/Old.php');
    $this->assertFileExists($this->app . '/views/old.html.php');
  }
}
