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
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'oevattbe',
    'title'        => 'VAT TBE services',
    'description'  => array(
        'de' => 'Module for VAT TBE Services.',
        'en' => 'Module for VAT TBE Services.',
    ),
    'thumbnail'    => 'logo.jpg',
    'version'      => '1.0.0',
    'author'       => 'OXID eSales AG',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'extend'       => array(
        'oxarticle'         => 'oe/oevattbe/models/oevattbeoxarticle',
        'oxarticlelist'     => 'oe/oevattbe/models/oevattbeoxarticlelist',
        'oxuser'            => 'oe/oevattbe/models/oevattbeoxuser',
        'oxsearch'          => 'oe/oevattbe/models/oevattbeoxsearch',
        'oxvatselector'     => 'oe/oevattbe/models/oevattbeoxvatselector',
        'oxbasket'          => 'oe/oevattbe/models/oevattbeoxbasket',
        'oxcmp_basket'      => 'oe/oevattbe/components/oevattbeoxcmp_basket',
        'oxorder'           => 'oe/oevattbe/models/oevattbeoxorder',
    ),
    'files' => array(
        'oeVATTBEEvidenceSelector'         => 'oe/oevattbe/models/oevattbeevidenceselector.php',
        'oeVATTBEEvidenceCollector'        => 'oe/oevattbe/models/oevattbeevidencecollector.php',
        'oeVATTBEEvidence'                 => 'oe/oevattbe/models/evidences/oevattbeevidence.php',
        'oeVATTBEBillingCountryEvidence'   => 'oe/oevattbe/models/evidences/oevattbebillingcountryevidence.php',
        'oeVATTBEGeoLocationEvidence'      => 'oe/oevattbe/models/evidences/oevattbegeolocationevidence.php',
        'oeVATTBEEvidenceList'             => 'oe/oevattbe/models/evidences/oevattbeevidencelist.php',
        'oeVATTBEList'                     => 'oe/oevattbe/models/oevattbelist.php',
        'oeVatTbeEvents'                   => 'oe/oevattbe/core/oevattbeevents.php',
        'oeVATTBETBEUser'                  => 'oe/oevattbe/models/oevattbetbeuser.php',
        'oeVATTBEOrderArticleChecker'      => 'oe/oevattbe/models/oevattbeorderarticlechecker.php',
    ),
    'events'       => array(
        'onActivate'   => 'oeVatTbeEvents::onActivate',
        'onDeactivate' => 'oeVatTbeEvents::onDeactivate'
    ),
    'templates' => array(
    ),
    'blocks' => array(
        array('template' => 'layout/base.tpl', 'block'=>'base_style', 'file'=>'/views/blocks/layout/base.tpl'),
        array('template' => 'page/details/inc/productmain.tpl', 'block'=>'details_productmain_price_value', 'file'=>'/views/blocks/page/details/inc/productmain.tpl'),
        array('template' => 'widget/product/listitem_grid.tpl', 'block'=>'widget_product_listitem_grid_price_value', 'file'=>'/views/blocks/widget/product/listitem_grid.tpl'),
        array('template' => 'widget/product/listitem_infogrid.tpl', 'block'=>'widget_product_listitem_infogrid_price_value', 'file'=>'/views/blocks/widget/product/listitem_infogrid.tpl'),
        array('template' => 'widget/product/listitem_line.tpl', 'block'=>'widget_product_listitem_line_price_value', 'file'=>'/views/blocks/widget/product/listitem_line.tpl'),
        array('template' => 'widget/product/boxproduct.tpl', 'block'=>'widget_product_boxproduct_price_value', 'file'=>'/views/blocks/widget/product/boxproduct.tpl'),
        array('template' => 'widget/product/bargainitem.tpl', 'block'=>'widget_product_bargainitem_price_value', 'file'=>'/views/blocks/widget/product/bargainitem.tpl'),
        array('template' => 'widget/product/compareitem.tpl', 'block'=>'widget_product_compareitem_price_value', 'file'=>'/views/blocks/widget/product/compareitem.tpl'),
    ),
    'settings' => array(
        array('group' => 'oevattbe', 'name' => 'blOeVATTBECountryEvidences',      'type' => 'arr',   'value' => array('oeVATTBEBillingCountryEvidence', 'oeVATTBEGeoLocationEvidence')),
        array('group' => 'oevattbe', 'name' => 'sOeVATTBEDefaultEvidence',        'type' => 'str',   'value' => 'billing_country'),

    )
);
