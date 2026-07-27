<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Tests\Integration;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\Facts\Facts;

/**
 * Loads the edition specific, DML-only integration fixture dump. Shared by the transactional
 * BaseTestCase and the non-transactional LegacyBaseTestCase.
 */
trait FixtureLoadingTrait
{
    protected function loadEditionFixture(): void
    {
        $connection = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create()
            ->getConnection();

        $edition = (new Facts())->getEdition();

        $connection->executeStatement(
            file_get_contents(
                __DIR__ . '/../Fixtures/dump_' . strtolower($edition) . '.sql'
            )
        );

        Registry::getLang()->setBaseLanguage(0);
    }
}
