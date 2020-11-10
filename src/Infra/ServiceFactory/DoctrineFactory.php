<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class DoctrineFactory
{
    public function create(): Connection
    {

        $connectionParams = array(
            'url' => 'mysql://cafe-user:cafe-pass@cafe-mysql/cafe-db?charset=UTF8',
        );
        return  DriverManager::getConnection($connectionParams);

    }
}