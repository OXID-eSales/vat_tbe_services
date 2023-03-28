<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
$testConfig = new \OxidEsales\TestingLibrary\TestConfig();

$testDirectory = $testConfig->getEditionTestsPath($testConfig->getShopEdition());
$serviceCaller->setParameter('importSql', '@' . $testDirectory . '/Fixtures/testdata.sql');
$serviceCaller->callService('ShopPreparation', 1);

include_once 'libs/oevattbenullmailer.php';
