<?php

use Psr\Log\LoggerInterface;
use Src\Helpers\LoggerFactory;
use Src\Helpers\LogReader;

test('logger interface', function () {
    $logger = LoggerFactory::getLogger('test');

    expect($logger)->toBeInstanceOf(LoggerInterface::class);
});

test('logger message file', function () {
    $logger = LoggerFactory::getLogger('test');
    $logger->info('Test log message');

    $logContent = LogReader::read(1);

    expect($logContent[0])->toContain('Test log message');

    LogReader::deleteLogFiles();
});