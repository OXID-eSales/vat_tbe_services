<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Tests\Integration;

use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

/**
 * Transactional integration base (CE IntegrationTestCase): each test runs inside a rolled-back
 * DB transaction. Fixture is DML-only so it does not implicitly commit. Use this for tests that
 * do not run DDL and do not read written data back through OXID list loaders or a second
 * DB connection; those belong on LegacyBaseTestCase.
 */
abstract class BaseTestCase extends IntegrationTestCase
{
    use FixtureLoadingTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadEditionFixture();
    }
}
