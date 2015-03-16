<?php
/**
 * This file is part of OXID eSales eVAT module.
 *
 * OXID eSales eVAT module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales eVAT module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales eVAT module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Acceptance test cases
 */
class oeVATTBE_dummyTest extends oxTestCase
{

    const TEST_USER_NAME = 'test@oxid-esales.com';
    const TEST_USER_PASSWORD = 'testtest';

    /**
     * test for activating Module.
     *
     * @group mobile
     */
    public function testActivateExtension()
    {
        $this->open(shopURL . "admin");
        $this->loginAdminForModule("Extensions", "Modules", null, null, null, "admin", "admin");
        $this->openListItem("link=VAT TBE services");
        $this->clickAndWait("module_activate");
        // dumping database
        try {
            $this->dumpDB();
        } catch (Exception $e) {
            $this->stopTesting("Failed dumping original db");
        }
    }
}
