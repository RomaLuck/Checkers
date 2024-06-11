<?php

use Psr\Log\LoggerInterface;
use Src\Entity\Log;
use Src\Helpers\EntityManagerFactory;
use Src\Helpers\LoggerFactory;

test('logger interface', function () {
    $logger = LoggerFactory::getLogger('test');

    expect($logger)->toBeInstanceOf(LoggerInterface::class);
});

test('logger message', function () {
    $logger = LoggerFactory::getLogger('test');
    $logger->info('Test log message');

    $logs = EntityManagerFactory::create()->getRepository(Log::class)->findBy(['channel' => 'test']);
    $lastLog = $logs[array_key_last($logs)];

    expect($lastLog->getMessage())->toContain('Test log message');
});
