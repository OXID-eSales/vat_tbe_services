<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use OxidEsales\EVatModule\Model\CategoryArticlesUpdater;
use OxidEsales\EVatModule\Model\DbGateway\CategoryVATGroupsPopulatorDbGateway;
use OxidEsales\EVatModule\Shop\Category;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Testing CategoryArticlesUpdater class.
 *
 * @covers CategoryVATGroupsPopulator
 */
class CategoryVATGroupsPopulatorTest extends TestCase
{
//    /**
//     * Tests creating of CategoryArticlesUpdater.
//     */
//    public function testCreating()
//    {
//        $oArticlesUpdater = CategoryArticlesUpdater::createInstance();
//
//        $this->assertInstanceOf(CategoryArticlesUpdater::class, $oArticlesUpdater);
//    }

    /**
     * Test deleting category groups list.
     */
    public function testDeletingCategoryVATGroupsList()
    {
        $oCategory = oxNew(Category::class);
        $oCategory->setId('categoryId');

        /** @var CategoryVATGroupsPopulatorDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(CategoryVATGroupsPopulatorDbGateway::class, ['populate']);
        $oGateway->expects($this->once())->method('populate')->with('categoryId');

        /** @var CategoryArticlesUpdater $oArticlesUpdater */
        $oArticlesUpdater = oxNew(CategoryArticlesUpdater::class, $oGateway);
        $oArticlesUpdater->addCategoryTBEInformationToArticles($oCategory);
    }

    /**
     * Test if DB gateway method was called.
     */
    public function testResetArticles()
    {
        $aArticles = [
            '_testId'
        ];
        /** @var CategoryVATGroupsPopulatorDbGateway|MockObject $oGateway */
        $oGateway = $this->createPartialMock(CategoryVATGroupsPopulatorDbGateway::class, ['reset']);
        $oGateway->expects($this->once())->method('reset')->with($aArticles);

        /** @var CategoryArticlesUpdater $oArticlesUpdater */
        $oArticlesUpdater = oxNew(CategoryArticlesUpdater::class, $oGateway);
        $oArticlesUpdater->removeCategoryTBEInformationFromArticles($aArticles);
    }
}
