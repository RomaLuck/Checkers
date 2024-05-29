<?php

declare(strict_types=1);

namespace Src\Helpers;

use Symfony\Component\Finder\Finder;

class LogReader
{
    public static function read(int $linesNum): array
    {
        $logs = [];

        $logFiles = self::getLogFiles();

        foreach ($logFiles as $logFile) {
            $splitContent = explode("\n", $logFile->getContents());

            foreach ($splitContent as $item) {
                if ($item !== '') {
                    $logs[] = $item;
                }
            }
        }

        $maxLines = count($logs);
        if ($maxLines !== null && $maxLines < $linesNum) {
            $linesNum = $maxLines;
        }

        return array_slice($logs, -1 * $linesNum);
    }

    public static function deleteLogFiles(): void
    {
        $logFiles = self::getLogFiles();

        foreach ($logFiles as $logFile) {
            unlink($logFile->getRealPath());
        }
    }

    /**
     * @return Finder
     */
    public static function getLogFiles(): Finder
    {
        $finder = new Finder();

        return $finder->files()
            ->in(__DIR__ . '/../../logs')
            ->name('*.log')
            ->date('since today');
    }
}