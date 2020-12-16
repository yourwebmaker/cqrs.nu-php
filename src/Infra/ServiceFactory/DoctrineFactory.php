<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DoctrineFactory
{
    public function create(string $databaseDns = ''): Connection
    {
        return  DriverManager::getConnection([
            'url' => $databaseDns,
        ]);
    }
}