<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;

/**
 * Testing oeVATTBECategoryArticlesUpdater class.
 *
 * @covers CategoryVATGroupsPopulator
 */
class CategoryVATGroupsPopulatorTest extends TestCase
{
    /**
     * Tests creating of oeVATTBECategoryArticlesUpdater.
     */
    public function testCreating()
    {
        $oArticlesUpdater = oeVATTBECategoryArticlesUpdater::createInstance();

        $this->assertInstanceOf('oeVATTBECategoryArticlesUpdater', $oArticlesUpdater);
    }

    /**
     * Test deleting category groups list.
     */
    public function testDeletingCategoryVATGroupsList()
    {
        $oCategory = oxNew('oeVATTBEoxCategory');
        $oCategory->setId('categoryId');

        /** @var oeVATTBECategoryVATGroupsPopulatorDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsPopulatorDbGateway', array('populate'));
        $oGateway->expects($this->once())->method('populate')->with('categoryId');

        /** @var oeVATTBECategoryArticlesUpdater $oArticlesUpdater */
        $oArticlesUpdater = oxNew('oeVATTBECategoryArticlesUpdater', $oGateway);
        $oArticlesUpdater->addCategoryTBEInformationToArticles($oCategory);
    }

    /**
     * Test if DB gateway method was called.
     */
    public function testResetArticles()
    {
        $aArticles = array(
            '_testId'
        );
        /** @var oeVATTBECategoryVATGroupsPopulatorDbGateway|PHPUnit_Framework_MockObject_MockObject $oGateway */
        $oGateway = $this->getMock('oeVATTBECategoryVATGroupsPopulatorDbGateway', array('reset'));
        $oGateway->expects($this->once())->method('reset')->with($aArticles);

        /** @var oeVATTBECategoryArticlesUpdater $oArticlesUpdater */
        $oArticlesUpdater = oxNew('oeVATTBECategoryArticlesUpdater', $oGateway);
        $oArticlesUpdater->removeCategoryTBEInformationFromArticles($aArticles);
    }
}
