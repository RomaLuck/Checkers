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

    $lastLog = EntityManagerFactory::create()->getRepository(Log::class)->findOneBy(['channel' => 'test'], ['id' => 'DESC']);

    expect($lastLog->getMessage())->toContain('Test log message');
});
