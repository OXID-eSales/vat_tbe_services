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
 * Testing oeVATTBEOrderEvidenceList class.
 */
class Integration_oeVatTbe_OrderEvidenceList_oeVATTBEOrderEvidenceListTest extends OxidTestCase
{
    /**
     * @return oeVATTBEOrderEvidenceList
     */
    public function testSavingEvidenceList()
    {
        $aData = array('evidence' => 'evidenceData');
        $oGateway = $this->getMock('TestGateway', array('save'));

        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList', $oGateway);

        $oList->setId('order_id');
        $oList->setData($aData);

        $oList->save();

        return $oList;
    }

    /**
     * @depends testSavingEvidenceList
     *
     * @param oeVATTBEOrderEvidenceList $oList
     *
     * @return oeVATTBEOrderEvidenceList
     */
    public function testLoadingEvidenceList($oList)
    {
        /** @var oeVATTBEOrderEvidenceList $oList */
        $oList = oxNew('oeVATTBEOrderEvidenceList');
        $oList->load('order_id');

        $aData = $oList->getData();

        $aExpectedData = array(
            'evidence1' => array(
                'name' => 'evidence1',
                'countryId' => 'GermanyId',
                'timestamp' => $aData['evidence1']['timestamp'],
            ),
            'evidence2' => array(
                'name' => 'evidence2',
                'countryId' => 'GermanyId',
                'timestamp' => $aData['evidence2']['timestamp'],
            ),
        );

        $this->assertEquals($aExpectedData, $aData);

        return $oList;
    }

    /**
     * @depends testSavingEvidenceList
     *
     * @param oeVATTBEOrderEvidenceList $oList
     */
    public function testDeletingEvidenceList($oList)
    {
        $oList->delete();
        $oList->load();
        $this->assertEquals(array(), $oList->getData());
    }
}
