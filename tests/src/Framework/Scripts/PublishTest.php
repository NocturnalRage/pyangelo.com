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
      // Not in git: only the server has it.
      'config/CloudFront/pk-cloudfront.pem' => 'server key',
      'public/build/assets/js/app.js-OLD.js' => 'previous app',
      'public/build/assets/js/stale-OLD.js' => 'app from an old release',
      'public/build/.vite/manifest.json' => '{"release":"old"}',
      'public/images/old.png' => 'old image',
      'public/samples/old.wav' => 'old sample',
      'public/index.php' => 'old front controller',
      'public/uploads/images/blog_thumbnail/post.png' => 'an uploaded image',
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

  public function testKeepsTheFilesTheRetiredBuildUsesEvenWhenTheyAreOld() {
    // The live build was deployed two weeks ago, so its files are old on disk,
    // but a page opened just before this deploy still needs them.
    $this->write($this->app, [
      'public/build/.vite/manifest.json' => json_encode([
        'resources/assets/js/app.js' => [
          'file' => 'assets/js/app.js-LIVE.js',
          'isEntry' => true,
          'imports' => ['_jquery-LIVE.js'],
          'css' => ['assets/app-LIVE.css']
        ],
        '_jquery-LIVE.js' => ['file' => 'assets/jquery-LIVE.js'],
        'resources/assets/sass/app.scss' => [
          'file' => 'assets/sass/app-LIVE.css',
          'isEntry' => true,
          'assets' => ['assets/fontawesome-webfont-LIVE.woff2']
        ],
        // Paths outside public/build are never touched.
        'outside' => ['file' => '../../outside.txt']
      ]),
      'public/build/assets/js/app.js-LIVE.js' => 'live app',
      'public/build/assets/jquery-LIVE.js' => 'live jquery',
      'public/build/assets/app-LIVE.css' => 'live css',
      'public/build/assets/sass/app-LIVE.css' => 'live stylesheet',
      'public/build/assets/fontawesome-webfont-LIVE.woff2' => 'live font',
      'outside.txt' => 'not part of the build'
    ]);
    $live = [
      'assets/js/app.js-LIVE.js', 'assets/jquery-LIVE.js', 'assets/app-LIVE.css',
      'assets/sass/app-LIVE.css', 'assets/fontawesome-webfont-LIVE.woff2'
    ];
    foreach ($live as $file) {
      touch($this->app . '/public/build/' . $file, strtotime('-14 days'));
    }
    $outsideTime = strtotime('-20 days');
    touch($this->app . '/outside.txt', $outsideTime);

    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);

    foreach ($live as $file) {
      $this->assertFileExists($this->app . '/public/build/' . $file);
    }
    clearstatcache();
    $this->assertSame($outsideTime, filemtime($this->app . '/outside.txt'));
    // A file no build has listed for over a week is still removed.
    $this->assertFileDoesNotExist($this->app . '/public/build/assets/js/stale-OLD.js');
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"new"}');
  }

  public function testRetiredFilesAreRemovedOnceTheyHaveBeenRetiredForAWeek() {
    $this->write($this->app, [
      'public/build/.vite/manifest.json' => json_encode([
        'resources/assets/js/app.js' => ['file' => 'assets/js/app.js-LIVE.js', 'isEntry' => true]
      ]),
      'public/build/assets/js/app.js-LIVE.js' => 'live app'
    ]);
    touch($this->app . '/public/build/assets/js/app.js-LIVE.js', strtotime('-14 days'));

    // This publish retires app.js-LIVE.js and starts its week.
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $retired = $this->app . '/public/build/assets/js/app.js-LIVE.js';
    $this->assertFileExists($retired);
    clearstatcache();
    $this->assertGreaterThan(strtotime('-1 minute'), filemtime($retired));

    // Eight days later the next publish removes it.
    touch($retired, strtotime('-8 days'));
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileDoesNotExist($retired);
  }

  public function testAnUnreadableLiveManifestDoesNotStopThePublish() {
    $this->write($this->app, ['public/build/.vite/manifest.json' => 'not json']);
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"new"}');
  }

  public function testNeverRewritesABuildFileThatIsAlreadyLive() {
    // The same content-hashed name in both builds means the same contents,
    // and the site may be serving the live copy while publish runs.
    // In real builds the contents match; here they differ only so a rewrite
    // shows. cp overwrites in place on the same inode, so the inode would not.
    $this->write($this->git, ['public/build/assets/jquery-SAME.js' => 'jquery from the new build']);
    $this->write($this->app, ['public/build/assets/jquery-SAME.js' => 'jquery being served']);
    $live = $this->app . '/public/build/assets/jquery-SAME.js';
    // It was copied by a release long ago and is not in the live manifest.
    touch($live, strtotime('-10 days'));

    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);

    clearstatcache();
    $this->assertStringEqualsFile($live, 'jquery being served', 'the live file was rewritten');
    // It is part of this build, so it is not deleted as old.
    $this->assertGreaterThan(strtotime('-1 minute'), filemtime($live));
  }

  public function testLeavesNoTemporaryBuildFiles() {
    $this->write($this->git, ['public/build/assets/fonts/icon-NEW.woff2' => 'font']);
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertStringEqualsFile($this->app . '/public/build/assets/fonts/icon-NEW.woff2', 'font');
    exec('find ' . escapeshellarg($this->app . '/public/build') . ' -name "*.tmp"', $temporary);
    $this->assertSame([], $temporary);
  }

  public function testAFailureWhileCopyingLeavesTheLiveReleaseUnchanged() {
    // The manifest must not go live unless the templates and code that use
    // it can go live with it.
    exec('rm -rf ' . escapeshellarg($this->git . '/views'));
    [$status] = $this->publish();
    $this->assertNotSame(0, $status);
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"old"}');
    $this->assertStringEqualsFile($this->app . '/config/app.php', 'old config');
    $this->assertFileExists($this->app . '/src/Old.php');
    $this->assertFileExists($this->app . '/views/old.html.php');
    $this->assertStringEqualsFile($this->app . '/public/index.php', 'old front controller');
    $this->assertFileExists($this->app . '/public/images/old.png');
  }

  private function assertLiveReleaseUnchanged(): void {
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"old"}');
    $this->assertStringEqualsFile($this->app . '/config/app.php', 'old config');
    $this->assertStringEqualsFile($this->app . '/config/CloudFront/pk-cloudfront.pem', 'server key');
    $this->assertFileExists($this->app . '/src/Old.php');
    $this->assertFileExists($this->app . '/views/old.html.php');
    $this->assertStringEqualsFile($this->app . '/public/index.php', 'old front controller');
    $this->assertFileDoesNotExist($this->app . '/public/build/.vite/manifest.json.new');
  }

  public function testStopsWhenTheBuildHasNoAssetsDirectory() {
    exec('rm -rf ' . escapeshellarg($this->git . '/public/build/assets'));
    [$status, $output] = $this->publish();
    $this->assertNotSame(0, $status, $output);
    $this->assertLiveReleaseUnchanged();
  }

  public function testStopsWhenTheNewManifestListsAFileTheBuildLacks() {
    $this->write($this->git, ['public/build/.vite/manifest.json' => json_encode([
      'resources/assets/js/app.js' => ['file' => 'assets/js/app.js-NEW.js', 'isEntry' => true],
      'resources/assets/js/quiz.js' => ['file' => 'assets/js/quiz.js-MISSING.js', 'isEntry' => true]
    ])]);
    [$status, $output] = $this->publish();
    $this->assertNotSame(0, $status);
    $this->assertStringContainsString('assets/js/quiz.js-MISSING.js', $output);
    $this->assertLiveReleaseUnchanged();
  }

  public function testStopsWhenTheNewManifestCannotBeRead() {
    $this->write($this->git, ['public/build/.vite/manifest.json' => 'not json']);
    [$status, $output] = $this->publish();
    $this->assertNotSame(0, $status, $output);
    $this->assertLiveReleaseUnchanged();
  }

  public function testPublishesWhenEveryFileTheNewManifestListsIsPresent() {
    $this->write($this->git, ['public/build/.vite/manifest.json' => json_encode([
      'resources/assets/js/app.js' => ['file' => 'assets/js/app.js-NEW.js', 'isEntry' => true]
    ])]);
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileExists($this->app . '/src/New.php');
  }

  /**
   * Puts an mv on PATH that refuses to move the live NAME directory, and
   * optionally rejects --exchange like coreutils before 9.5.
   */
  private function failingMv(string $name, bool $withoutExchange = false): string {
    $bin = $this->root . '/bin';
    mkdir($bin);
    $realMv = trim(shell_exec('command -v mv'));
    $target = $this->app . '/' . $name;
    $exchange = $withoutExchange ? " || [ \"\$arg\" = --exchange ]" : '';
    file_put_contents($bin . '/mv', "#!/usr/bin/env bash\n" .
      "for arg in \"\$@\"; do { [ \"\$arg\" = " . escapeshellarg($target) . " ]$exchange; } && { echo 'mv: refused' >&2; exit 1; }; done\n" .
      "exec " . escapeshellarg($realMv) . " \"\$@\"\n");
    chmod($bin . '/mv', 0755);
    return 'PATH=' . escapeshellarg($bin . ':' . getenv('PATH')) . ' ';
  }

  /** After a failed switch, the whole old release is live again. */
  private function assertSwitchedBack(string $output): void {
    $this->assertStringContainsString('the previous manifest, config/, src/ and views/ are back in place', $output);
    $this->assertStringEqualsFile($this->app . '/public/build/.vite/manifest.json', '{"release":"old"}');
    $this->assertStringEqualsFile($this->app . '/config/app.php', 'old config');
    $this->assertStringEqualsFile($this->app . '/config/CloudFront/pk-cloudfront.pem', 'server key');
    $this->assertFileExists($this->app . '/src/Old.php');
    $this->assertFileDoesNotExist($this->app . '/src/New.php');
    $this->assertFileExists($this->app . '/views/old.html.php');
    $this->assertFileDoesNotExist($this->app . '/public/build/.vite/manifest.json.previous');
  }

  public function testSwitchesEverythingBackIfViewsCannotBeSwapped() {
    // config/ and src/ were already switched when views/ failed.
    [$status, $output] = $this->publish(null, null, $this->failingMv('views'));
    $this->assertNotSame(0, $status, $output);
    $this->assertSwitchedBack($output);
  }

  public function testSwitchesEverythingBackWithoutMvExchange() {
    [$status, $output] = $this->publish(null, null, $this->failingMv('views', true));
    $this->assertNotSame(0, $status, $output);
    $this->assertSwitchedBack($output);
  }

  public function testKeepsServerOnlyConfigFiles() {
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertStringEqualsFile($this->app . '/config/app.php', 'new config');
    $this->assertStringEqualsFile($this->app . '/config/CloudFront/pk-cloudfront.pem', 'server key');
    $this->assertSame([], $this->leftovers());
  }

  public function testSwitchesEverythingBackIfSrcCannotBeSwapped() {
    [$status, $output] = $this->publish(null, null, $this->failingMv('src'));
    $this->assertNotSame(0, $status, $output);
    $this->assertSwitchedBack($output);
  }

  public function testNeverRemovesFilesFromTheBuildBeingPublished() {
    // A file in this build can be old on disk if nothing changed it; it gets
    // the current time on the live site, so pruning keeps it.
    touch($this->git . '/public/build/assets/js/app.js-NEW.js', strtotime('-30 days'));
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertFileExists($this->app . '/public/build/assets/js/app.js-NEW.js');
  }

  public function testLeavesOtherPublicFilesAlone() {
    // public/uploads holds files people uploaded; it is not part of a release.
    [$status, $output] = $this->publish();
    $this->assertSame(0, $status, $output);
    $this->assertStringEqualsFile($this->app . '/public/uploads/images/blog_thumbnail/post.png', 'an uploaded image');
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
