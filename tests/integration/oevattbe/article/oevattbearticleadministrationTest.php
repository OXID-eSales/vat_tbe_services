<?php
/**
 * This file is part of OXID eSales VAT TBE module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales VAT TBE module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */


/**
 * Testing VAT TBE administration in article page.
 */
class Integration_oeVATTBE_article_oeVATTBEArticleAdministrationTest extends OxidTestCase
{
    /**
     * Check if view data is correct.
     */
    public function testViewData()
    {
        $this->_prepareData();

        /** @var oeVATTBEArticleAdministration $oArticleAdministration */
        $oArticleAdministration = oxNew('oeVATTBEArticleAdministration');
        $oArticleAdministration->render();
        $aViewData = $oArticleAdministration->getViewData();

        $aExpectedViewData = array(
            'a7c40f631fc920687.20179984' => array(
                'a7c40f631fc920687.20179984' => 'Deutschland',
                '2' => 'Group Name1 - 20.50%'
            ),
            'a7c40f6323c4bfb36.59919433' => array(
                'a7c40f6323c4bfb36.59919433' => 'Italien',
                '3' => 'Group Name2 - 11.11%'
            ),
        );

        $this->assertSame($aExpectedViewData, $aViewData['aTBECountries'], 'Data which should go ');
    }

    /**
     * Prepares VAT TBE groups data.
     */
    private function _prepareData()
    {
        /** @var oeVATTBECountryVATGroupsDbGateway $oGateway */
        $oGateway = oxNew('oeVATTBECountryVATGroupsDbGateway');
        foreach ($oGateway->getList() as $aGroupInformation) {
            $oGateway->delete($aGroupInformation['OEVATTBE_ID']);
        }
        $aData1 = array(
            'oevattbe_id'          => '2',
            'oevattbe_countryid'   => 'a7c40f631fc920687.20179984',
            'oevattbe_name'        => 'Group Name1',
            'oevattbe_description' => 'Some description1',
            'oevattbe_rate'        => 20.50
        );
        $oGateway->save($aData1);

        $aData2 = array(
            'oevattbe_id'          => '3',
            'oevattbe_countryid'   => 'a7c40f6323c4bfb36.59919433',
            'oevattbe_name'        => 'Group Name2',
            'oevattbe_description' => 'Some description2',
            'oevattbe_rate'        => 11.11
        );
        $oGateway->save($aData2);
    }
}
