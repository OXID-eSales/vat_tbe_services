<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $facts = new Facts();
        $edition = $facts->getEdition();

        $connection->executeStatement(
            file_get_contents(
                __DIR__ . '/../Fixtures/dump_' . strtolower($edition) . '.sql'
            )
        );
    }
}
