<?php

namespace Src\Helpers;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Dotenv\Dotenv;

class EntityManagerFactory
{
    public const PATH = __DIR__ . '/../Entity';

    public static function create(): EntityManagerInterface
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');

        $dbData = parse_url($_ENV['DOCTRINE_URL']);
        parse_str($dbData['query'], $queryData);

        $dbParams = [
            'driver' => 'pdo_' . $dbData['scheme'],
            'host' => $dbData['host'],
            'port' => $dbData['port'],
            'user' => $dbData['user'],
            'password' => $dbData['pass'],
            'dbname' => ltrim($dbData['path'], '/'),
            'charset' => $queryData['charset']
        ];

        $config = ORMSetup::createAttributeMetadataConfiguration([self::PATH], $dbData['fragment']);
        $connection = DriverManager::getConnection($dbParams, $config);

        return new EntityManager($connection, $config);
    }
}