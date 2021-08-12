<?php

declare(strict_types=1);

namespace GimmeBook\Infrastructure\Core\Doctrine\Factory;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class EntityManagerFactory
{
    private string $dbName;
    private string $user;
    private string $password;
    private string $host;

    public function __construct(string $dbName, string $user, string $password, string $host)
    {
        $this->dbName = $dbName;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
    }

    public function createEntityManager(): EntityManager
    {
        $config = Setup::createYAMLMetadataConfiguration(['/var/www/config/mappings'], true);
        $connection = DriverManager::getConnection(
            [
                'dbname' => $this->dbName,
                'user' => $this->user,
                'password' => $this->password,
                'host' => $this->host,
                'driver' => 'pdo_mysql',
            ]
        );
        return EntityManager::create($connection, $config);
    }
}
