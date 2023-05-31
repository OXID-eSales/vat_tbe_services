<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

trait DemoData
{
    public function setUp(): void
    {
        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $connection->executeStatement(
            file_get_contents(
                __DIR__ . '/../Fixtures/dump.sql'
            )
        );

        parent::setUp();
    }
}