<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Category;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Controller\Admin\CategoryAdministration;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Model\GroupArticleCacheInvalidator;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing VAT TBE administration in category page.
 */
class CategoryAdministrationTest extends BaseTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        ContainerFactory::resetContainer();
    }

    public function tearDown(): void
    {
        Registry::getSession()->setAdminMode(false);
    }

    /**
     * Check if view data is correct.
     */
    public function testViewData()
    {
        $aData1 = array(
            'OEVATTBE_ID'          => 2,
            'OEVATTBE_COUNTRYID'   => 'a7c40f631fc920687.20179984',
            'OEVATTBE_NAME'        => 'Group Name1',
            'OEVATTBE_DESCRIPTION' => 'Some description1',
            'OEVATTBE_RATE'        => '20.50',
            'OEVATTBE_TIMESTAMP'   => '2014-10-24 09:46:11'
        );
        $aData2 = array(
            'OEVATTBE_ID'          => 3,
            'OEVATTBE_COUNTRYID'   => 'a7c40f6323c4bfb36.59919433',
            'OEVATTBE_NAME'        => 'Group Name2',
            'OEVATTBE_DESCRIPTION' => 'Some description2',
            'OEVATTBE_RATE'        => '11.11',
            'OEVATTBE_TIMESTAMP'   => '2014-10-24 09:46:11'
        );
        $this->_cleanData();
        $this->_addData($aData1);
        $this->_addData($aData2);

        /** @var CategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew(CategoryAdministration::class);

        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        /** @var GroupArticleCacheInvalidator $groupArticleCacheInvalidator */
        $groupArticleCacheInvalidator = $this->get(GroupArticleCacheInvalidator::class);

        $oCountryVATGroup1 = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = oxNew(CountryVATGroup::class, $oGateway, $groupArticleCacheInvalidator);
        $oCountryVATGroup2->setId(3);
        $oCountryVATGroup2->setData($aData2);

        $aExpectedViewData = array(
            'a7c40f631fc920687.20179984' => array(
                'countryTitle' => 'Deutschland',
                'countryGroups' => array (
                    $oCountryVATGroup1
                ),
            ),
            'a7c40f6323c4bfb36.59919433' => array(
                'countryTitle' => 'Italien',
                'countryGroups' => array (
                    $oCountryVATGroup2
                ),
            ),
        );

        $this->assertEquals($aExpectedViewData, $oCategoryAdministration->getCountryAndVATGroupsData(), 'Data which should go to template is not correct.');
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function providerViewDataIsTBEService()
    {
        return array(
            /** TBE Service */
            array(1),
            /** Not TBE Service */
            array(0),
        );
    }

    /**
     * Check view data for correct value which shows if category is TBE.
     *
     * @param int $iIsTBECategory is tbe or not
     *
     * @dataProvider providerViewDataIsTBEService
     */
    public function testViewDataIsTBEService($iIsTBECategory)
    {
        /** @var Category $oCategory */
        $oCategory = oxNew(Category::class);
        $oCategory->setId('_testCategory');
        $oCategory->assign([
            'oxtitle' => '',
            'oevattbe_istbe' => $iIsTBECategory,
            'oxparentid'     => 'oxrootid',
        ]);
        $oCategory->save();

        /** @var CategoryAdministration $oCategoriesAdministration */
        $oCategoriesAdministration = oxNew(CategoryAdministration::class);
        $oCategoriesAdministration->setEditObjectId('_testCategory');

        $this->assertSame($iIsTBECategory, $oCategoriesAdministration->isCategoryTBE());
    }

    /**
     * Checks if selected option is saved rate.
     *
     * @return CategoryAdministration
     */
    public function testSelectedRateForCountry()
    {
        $_POST['editval'] = [
            'oevattbe_istbe' => false
        ];

        /** @var CategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew(CategoryAdministration::class);
        $aSelectParams = array(
            'a7c40f632e04633c9.47194042' => 2,
            '8f241f110955d3260.55487539' => ''
        );

        $_POST['VATGroupsByCountry'] = $aSelectParams;
        $oCategoryAdministration->setEditObjectId('_testCategory');
        $oCategoryAdministration->save();

        $this->assertSame(true, $oCategoryAdministration->isSelected('a7c40f632e04633c9.47194042', 2));

        return $oCategoryAdministration;
    }

    /**
     * Checks if rate was not selected.
     *
     * @param CategoryAdministration $oCategoryAdministration controller
     *
     * @depends testSelectedRateForCountry
     *
     * @return CategoryAdministration
     */
    public function testNotSelectedRateForCountry($oCategoryAdministration)
    {
        $this->assertFalse($oCategoryAdministration->isSelected('8f241f110955d3260.55487539', ''));

        return $oCategoryAdministration;
    }

    /**
     * Checks if method returns correct value for non existing country.
     *
     * @param CategoryAdministration $oCategoryAdministration controller
     *
     * @depends testNotSelectedRateForCountry
     */
    public function testSelectionForNonExistingCountry($oCategoryAdministration)
    {
        $this->assertSame(false, $oCategoryAdministration->isSelected('NoneExistingId', 2));
    }

    /**
     * Prepares VAT TBE groups data.
     *
     * @param array $aData
     */
    private function _addData($aData)
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);

        $oGateway->save($aData);
    }

    /**
     * Cleans current data.
     */
    private function _cleanData()
    {
        /** @var CountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew(CountryVATGroupsDbGateway::class);
        foreach ($oGateway->getList() as $aGroupInformation) {
            $oGateway->delete($aGroupInformation['OEVATTBE_ID']);
        }
    }
}
