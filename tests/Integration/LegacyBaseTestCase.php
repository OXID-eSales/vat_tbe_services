<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EVatModule\Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Non-transactional base for tests that the CE transaction model cannot cover (documented
 * exceptions): they read written data back through OXID list loaders, or reconnect / use a
 * second DB connection during the test. These rely on a freshly reset database between runs.
 */
abstract class LegacyBaseTestCase extends TestCase
{
    use FixtureLoadingTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->loadEditionFixture();
    }
}
