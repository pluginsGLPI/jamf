<?php

/**
 * -------------------------------------------------------------------------
 * JAMF plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of JAMF plugin for GLPI.
 *
 * JAMF plugin for GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * JAMF plugin for GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with JAMF plugin for GLPI. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2024-2025 by Teclib'
 * @copyright Copyright (C) 2019-2024 by Curtis Conard
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/jamf
 * -------------------------------------------------------------------------
 */

namespace GlpiPlugin\Jamf\Tests\units;

use DBmysql;
use PHPUnit\Framework\TestCase;
use PluginJamfMigration;

/**
 * Does not extend AbstractDBTest: ALTER TABLE implicitly commits, so a wrapping transaction cannot be used.
 */
class PluginJamfMigrationTest extends TestCase
{
    public function testJamfItemsIdIsMigratedToUnsigned(): void
    {
        /** @var DBmysql $DB */
        global $DB;

        // Restore the legacy definition and a legacy -1 value
        $DB->doQuery('ALTER TABLE `glpi_plugin_jamf_devices` MODIFY `jamf_items_id` int(11) NOT NULL DEFAULT -1');
        $DB->insert('glpi_plugin_jamf_devices', [
            'jamf_items_id' => -1,
            'items_id'      => 999999,
            'itemtype'      => 'Phone',
            'udid'          => 'test-unsigned-migration',
        ]);
        $device_id = $DB->insertId();

        try {
            $this->assertContains('glpi_plugin_jamf_devices.jamf_items_id', $this->getSignedKeysColumns());

            (new PluginJamfMigration(PLUGIN_JAMF_VERSION))->apply_3_2_4_migration();

            $column = $DB->request([
                'SELECT' => ['COLUMN_TYPE', 'COLUMN_DEFAULT'],
                'FROM'   => 'information_schema.columns',
                'WHERE'  => [
                    'TABLE_SCHEMA' => $DB->dbdefault,
                    'TABLE_NAME'   => 'glpi_plugin_jamf_devices',
                    'COLUMN_NAME'  => 'jamf_items_id',
                ],
            ])->current();
            $this->assertStringContainsString('unsigned', $column['COLUMN_TYPE']);
            $this->assertEquals('0', $column['COLUMN_DEFAULT']);
            $this->assertNotContains('glpi_plugin_jamf_devices.jamf_items_id', $this->getSignedKeysColumns());

            $device = $DB->request(['FROM' => 'glpi_plugin_jamf_devices', 'WHERE' => ['id' => $device_id]])->current();
            $this->assertEquals(0, $device['jamf_items_id']);
        } finally {
            $DB->delete('glpi_plugin_jamf_devices', ['id' => $device_id]);
        }
    }

    private function getSignedKeysColumns(): array
    {
        /** @var DBmysql $DB */
        global $DB;

        $columns = [];
        foreach ($DB->getSignedKeysColumns() as $column) {
            $columns[] = $column['TABLE_NAME'] . '.' . $column['COLUMN_NAME'];
        }

        return $columns;
    }
}
