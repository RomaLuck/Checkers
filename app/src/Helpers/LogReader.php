<?php

namespace Src\Helpers;

use Symfony\Component\Finder\Finder;

class LogReader
{
    public static function read(int $linesNum): array
    {
        $logs = [];

        $finder = new Finder();
        $logFiles = $finder->files()
            ->in(__DIR__ . '/../../logs')
            ->name('*.log')
            ->date('since today');

        foreach ($logFiles as $logFile) {
            $splitContent = explode("\n", $logFile->getContents());

            foreach ($splitContent as $item) {
                $logs[] = $item;
            }
        }

        $maxLines = count($logs);
        if ($maxLines < $linesNum) {
            $linesNum = $maxLines;
        }

        return array_slice($logs, -1 * $linesNum);
    }
}