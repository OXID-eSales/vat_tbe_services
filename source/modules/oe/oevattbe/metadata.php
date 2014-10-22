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
        'basket'            => 'oe/oevattbe/controllers/oevattbebasket',
        'order'             => 'oe/oevattbe/controllers/oevattbeorder',
        'oxbasketcontentmarkgenerator' => 'oe/oevattbe/models/oevattbeoxbasketcontentmarkgenerator',
        'order_main' => 'oe/oevattbe/controllers/admin/oevattbeorder_main',
        'oxcountry'         => 'oe/oevattbe/models/oevattbeoxcountry',
    ),
    'files' => array(
        'oeVATTBEModel'                                => 'oe/oevattbe/core/oevattbemodel.php',
        'oeVATTBEModelDbGateway'                       => 'oe/oevattbe/core/oevattbemodeldbgateway.php',
        'oeVatTbeEvents'                               => 'oe/oevattbe/core/oevattbeevents.php',
        'oeVATTBEList'                                 => 'oe/oevattbe/models/oevattbelist.php',
        'oeVATTBEEvidence'                             => 'oe/oevattbe/models/evidences/items/oevattbeevidence.php',
        'oeVATTBEBillingCountryEvidence'               => 'oe/oevattbe/models/evidences/items/oevattbebillingcountryevidence.php',
        'oeVATTBEGeoLocationEvidence'                  => 'oe/oevattbe/models/evidences/items/oevattbegeolocationevidence.php',
        'oeVATTBEEvidenceList'                         => 'oe/oevattbe/models/evidences/oevattbeevidencelist.php',
        'oeVATTBEEvidenceRegister'                     => 'oe/oevattbe/models/evidences/oevattbeevidenceregister.php',
        'oeVATTBEEvidenceCollector'                    => 'oe/oevattbe/models/evidences/oevattbeevidencecollector.php',
        'oeVATTBEEvidenceSelector'                     => 'oe/oevattbe/models/evidences/oevattbeevidenceselector.php',
        'oeVATTBETBEUser'                              => 'oe/oevattbe/models/oevattbetbeuser.php',
        'oeVATTBEOrderArticleChecker'                  => 'oe/oevattbe/models/oevattbeorderarticlechecker.php',
        'oeVATTBEBasketItemsValidator'                 => 'oe/oevattbe/services/oevattbebasketitemsvalidator.php',
        'oeVATTBEIncorrectVATArticlesMessageFormatter' => 'oe/oevattbe/models/oevattbeincorrectvaatrticlesmessageformatter.php',
        'oeVATTBETBEArticleCacheKey'                   => 'oe/oevattbe/models/oevattbetbearticlecachekey.php',
        'oeVATTBEOrderEvidenceList'                    => 'oe/oevattbe/models/oevattbeorderevidencelist.php',
        'oeVATTBEOrderEvidenceListDbGateway'           => 'oe/oevattbe/models/dbgateways/oevattbeorderevidencelistdbgateway.php',
        'oeVATTBEVATGroupsDbGateway'                   => 'oe/oevattbe/models/dbgateways/oevattbevatgroupsdbgateway.php',
        'oeVATTBEVATGroup'                             => 'oe/oevattbe/models/oevattbevatgroup.php',
        'oeVATTBEVATGroupsList'                        => 'oe/oevattbe/models/oevattbecountryvatgroupslist.php',
        'oeVATTBEArticleSQLBuilder'                    => 'oe/oevattbe/models/oevattbearticlesqlbuilder.php',
        'oeVATTBECountryVatGroups'                     => 'oe/oevattbe/controllers/admin/oevattbecountryvatgroups.php',
        'oeVATTBEArticleAdministration'                => 'oe/oevattbe/controllers/admin/oevattbearticleadministration.php',
    ),
    'events'       => array(
        'onActivate'   => 'oeVatTbeEvents::onActivate',
        'onDeactivate' => 'oeVatTbeEvents::onDeactivate'
    ),
    'templates' => array(
        'oevattbecountryvatgroups.tpl' => 'oe/oevattbe/views/admin/tpl/oevattbecountryvatgroups.tpl'
        'oevattbearticleadministration.tpl' => 'oe/oevattbe/views/admin/tpl/oevattbearticleadministration.tpl',
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
        array('template' => 'user_main.tpl', 'block'=>'admin_user_main_form', 'file'=>'/views/blocks/admin/user_main.tpl'),
        array('template' => 'page/checkout/inc/basketcontents.tpl', 'block'=>'checkout_basketcontents_basketitem_vat', 'file'=>'/views/blocks/page/checkout/inc/basketitem_vat.tpl'),
        array('template' => 'page/checkout/basket.tpl', 'block'=>'checkout_basket_next_step_bottom', 'file'=>'/views/blocks/page/checkout/basket_nextstep.tpl'),
        array('template' => 'page/checkout/order.tpl', 'block'=>'checkout_order_next_step_bottom', 'file'=>'/views/blocks/page/checkout/order_nextstep.tpl'),
        array('template' => 'order_main.tpl', 'block'=>'admin_order_main_form', 'file'=>'/views/admin/tpl/oevattbeorder_main.tpl'),
        array('template' => 'layout/page.tpl', 'block'=>'content_main', 'file'=>'/views/blocks/layout/page.tpl'),
        array('template' => 'country_list.tpl', 'block'=>'admin_country_list_colgroup', 'file'=>'/views/blocks/admin/country_list_colgroup.tpl'),
        array('template' => 'country_list.tpl', 'block'=>'admin_country_list_filter', 'file'=>'/views/blocks/admin/country_list_filter.tpl'),
        array('template' => 'country_list.tpl', 'block'=>'admin_country_list_sorting', 'file'=>'/views/blocks/admin/country_list_sorting.tpl'),
        array('template' => 'country_list.tpl', 'block'=>'admin_country_list_item', 'file'=>'/views/blocks/admin/country_list_item.tpl'),
    ),
    'settings' => array(
        array('group' => 'oevattbe', 'name' => 'aOeVATTBECountryEvidences',      'type' => 'aarr',   'value' => array('billing_country' => 1, 'geo_location' => 1)),
        array('group' => 'oevattbe', 'name' => 'sOeVATTBEDefaultEvidence',        'type' => 'str',   'value' => 'billing_country'),
    )
);
