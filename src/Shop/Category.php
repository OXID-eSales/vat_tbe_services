<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EVatModule\Shop;

/**
 * VAT TBE oxCategory class
 */
class Category extends Category_parent
{
    /**
     * Return true if it is tbe category
     *
     * @return bool
     */
    public function isOeVATTBETBE()
    {
        return (bool) $this->getFieldData('oevattbe_istbe');
    }
}
