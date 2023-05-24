<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Tests\Integration\Category;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EVatModule\Controller\Admin\CategoryAdministration;
use OxidEsales\EVatModule\Model\CountryVATGroup;
use OxidEsales\EVatModule\Model\DbGateway\CountryVATGroupsDbGateway;
use OxidEsales\EVatModule\Shop\Category;
use OxidEsales\EVatModule\Tests\Integration\BaseTestCase;

/**
 * Testing VAT TBE administration in category page.
 */
class CategoryAdministrationTest extends BaseTestCase
{
    use ContainerTrait;

    /**
     * Check if view data is correct.
     */
    public function testViewData()
    {
        $aData1 = array(
            'oevattbe_id'          => '2',
            'oevattbe_countryid'   => 'a7c40f631fc920687.20179984',
            'oevattbe_name'        => 'Group Name1',
            'oevattbe_description' => 'Some description1',
            'oevattbe_rate'        => '20.50',
            'oevattbe_timestamp'   => '2014-10-24 09:46:11'
        );
        $aData2 = array(
            'oevattbe_id'          => '3',
            'oevattbe_countryid'   => 'a7c40f6323c4bfb36.59919433',
            'oevattbe_name'        => 'Group Name2',
            'oevattbe_description' => 'Some description2',
            'oevattbe_rate'        => '11.11',
            'oevattbe_timestamp'   => '2014-10-24 09:46:11'
        );
        $this->_cleanData();
        $this->_addData($aData1);
        $this->_addData($aData2);

        /** @var CategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew(CategoryAdministration::class);

        $oCountryVATGroup1 = $this->get(CountryVATGroup::class);
        $oCountryVATGroup1->setId(2);
        $oCountryVATGroup1->setData($aData1);

        $oCountryVATGroup2 = $this->get(CountryVATGroup::class);
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
    public function providerViewDataIsTBEService()
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
        $oCategory->oxcategories__oevattbe_istbe = new Field($iIsTBECategory);
        $oCategory->oxcategories__oxparentid = new Field('oxrootid');
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
        /** @var CategoryAdministration $oCategoryAdministration */
        $oCategoryAdministration = oxNew(CategoryAdministration::class);
        $aSelectParams = array(
            'a7c40f632e04633c9.47194042' => 2,
            '8f241f110955d3260.55487539' => ''
        );

        $_POST['VATGroupsByCountry'] = $aSelectParams;
        $oCategoryAdministration->setEditObjectId('_testCategory');
        $oCategoryAdministration->save();

        $this->assertSame(true, $oCategoryAdministration->isSelected('a7c40f632e04633c9.47194042', '2'));

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
        $this->assertSame(false, $oCategoryAdministration->isSelected('8f241f110955d3260.55487539', ''));

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
        $this->assertSame(false, $oCategoryAdministration->isSelected('NoneExistingId', '2'));
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
