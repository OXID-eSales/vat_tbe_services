<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * VAT TBE oxCategory class
 */
class oeVATTBEOxCategory extends oeVATTBEOxCategory_parent
{
    /**
     * Return true if it is tbe category
     *
     * @return bool
     */
    public function isOeVATTBETBE()
    {
        return (bool) $this->oxcategories__oevattbe_istbe->value;
    }
}
