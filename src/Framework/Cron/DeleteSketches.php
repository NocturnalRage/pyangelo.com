#!/usr/bin/php
<?php
namespace Framework\Cron;

require(dirname(__FILE__) . '/../../../vendor/autoload.php');
require(dirname(__FILE__) . '/../../../config/services.php');
use DateTime;

// ─── Load env & services ─────────────────────────────────────────────
$dotenv = $di->get('dotenv');
$dotenv->load();

$sketchRepository = $di->get('sketchRepository');
$logFile          = $_ENV['APPLICATION_LOG_FILE'];
$publicSketchDir  = $_ENV['APPLICATION_DIRECTORY'] . '/public/sketches';

// ─── Main purge function ────────────────────────────────────────────
function purgeDeletedSketches($sketchRepository, string $sketchDir, string $logFile)
{
    $cutoff = (new DateTime())->modify("-90 days")->format('Y-m-d H:i:s');
    logMessage("Starting purge: deleting sketches soft-deleted before {$cutoff}", 'INFO', $logFile);

    $sketchesToDelete = $sketchRepository->getSketchesToDelete();

    logMessage("Found " . count($sketchesToDelete) . " sketches to purge", 'INFO', $logFile);

    foreach ($sketchesToDelete as $sketch) {
        $sketchId  = $sketch['sketch_id'];
        $personId  = $sketch['person_id'];
        $path  = rtrim($sketchDir, '/') . "/{$personId}/{$sketchId}";

        try {
            if (is_dir($path)) {
                deleteDir($path);
                logMessage("Removed files: {$path}", 'INFO', $logFile);
            } else {
                logMessage("Path not found, skipping: {$path}", 'WARNING', $logFile);
            }
            $rowsDeleted = $sketchRepository->deleteSketchFiles($sketchId);
            if ($rowsDeleted > 0) {
                logMessage("Deleted DB records for {$rowsDeleted} files for sketch {$sketchId}", 'INFO', $logFile);
            } else {
                logMessage("Could NOT delete record for sketch files for sketch {$sketchId}", 'WARNING', $logFile);
            }
            $rowsDeleted = $sketchRepository->deleteSketchForever($sketchId);
            if ($rowsDeleted == 1) {
                logMessage("Deleted DB record for sketch {$sketchId}", 'INFO', $logFile);
            } else {
                logMessage("Could NOT delete record for sketch {$sketchId}", 'WARNING', $logFile);
            }

        } catch (\Exception $e) {
            logMessage("Failed to purge sketch {$sketchId}: " . $e->getMessage(), 'ERROR', $logFile);
        }
    }

    logMessage("Purge run complete", 'INFO', $logFile);
}

// ─── Recursively delete a directory ────────────────────────────────────────────
function deleteDir(string $dir)
{
    $it = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($dir);
}

// ─── Logger helper ─────────────────────────────────────────────────────────────
function logMessage(string $msg, string $level, string $logFile)
{
    $levels = ['DEBUG','INFO','WARNING','ERROR'];
    $lvl    = in_array($level, $levels) ? $level : 'INFO';
    $ts     = (new DateTime())->format('y-m-d H:i:s');
    $line   = "[{$ts}] {$lvl}: {$msg}" . PHP_EOL;
    file_put_contents($logFile, $line, FILE_APPEND);
}

// ─── Kick it off ───────────────────────────────────────────────────────────────
purgeDeletedSketches($sketchRepository, $publicSketchDir, $logFile);

