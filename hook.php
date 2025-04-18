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
 * @copyright Copyright (C) 2024-2024 by Teclib'
 * @copyright Copyright (C) 2019-2024 by Curtis Conard
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/jamf
 * -------------------------------------------------------------------------
 */

use Glpi\System\Status\StatusChecker;

function plugin_jamf_install()
{
    $jamfMigration = new PluginJamfMigration(PLUGIN_JAMF_VERSION);
    $jamfMigration->applyMigrations();

    return true;
}

function plugin_jamf_uninstall()
{
    $jamfMigration = new PluginJamfMigration(PLUGIN_JAMF_VERSION);
    $jamfMigration->uninstall();

    return true;
}

function plugin_jamf_getDatabaseRelations()
{
    //   $plugin = new Plugin();
    //   if ($plugin->isActivated('jamf')) {
    //      return [
    //         'glpi_softwares' => [
    //            'glpi_plugin_jamf_softwares' => 'softwares_id'
    //         ],
    //         'glpi_computers' => [
    //            'glpi_plugin_jamf_devices' => ['items_id', 'itemtype'],
    //         ],
    //         'glpi_phones' => [
    //            'glpi_plugin_jamf_devices' => ['items_id', 'itemtype'],
    //         ],
    //         'glpi_plugin_jamf_devices' => [
    //            'glpi_plugin_jamf_computers' => 'glpi_plugin_jamf_devices_id',
    //            'glpi_plugin_jamf_mobiledevices' => 'glpi_plugin_jamf_devices_id'
    //         ]
    //      ];
    //   }
    return [];
}

function plugin_jamf_getAddSearchOptions($itemtype)
{
    $opt    = [];
    $plugin = new Plugin();
    if ($plugin->isActivated('jamf')) {
        if ($itemtype === 'Computer' || $itemtype === 'Phone') {
            $opt = [
                '22002' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'last_inventory',
                    'name'          => 'Jamf - ' . _x('field', 'Last inventory', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                '22003' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'entry_date',
                    'name'          => 'Jamf - ' . _x('field', 'Import date', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                '22004' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'enroll_date',
                    'name'          => 'Jamf - ' . _x('field', 'Enrollment date', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                '22005' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'managed',
                    'name'          => 'Jamf - ' . _x('field', 'Managed', 'jamf'),
                    'datatype'      => 'bool',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                '22006' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'supervised',
                    'name'          => 'Jamf - ' . _x('field', 'Supervised', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                //            '22007' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'cloud_backup_enabled',
                //               'name'            => 'Jamf - ' ._x('field', 'Cloud backup enabled', 'jamf'),
                //               'datatype'        => 'bool',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                '22008' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'activation_lock_enabled',
                    'name'          => 'Jamf - ' . _x('field', 'Activation lock enabled', 'jamf'),
                    'datatype'      => 'bool',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                //            '22009' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_mode_enabled',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode enabled', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22010' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_mode_enforced',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode enforced', 'jamf'),
                //               'datatype'        => 'bool',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22011' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_mode_enable_date',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode enable date', 'jamf'),
                //               'datatype'        => 'datetime',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22012' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_mode_message',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode message', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22013' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_mode_phone',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode phone', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22014' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_location_latitude',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode latitude', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22015' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_location_longitude',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode longitude', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22016' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_location_altitude',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode altitude', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22017' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_location_speed',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode speed', 'jamf'),
                //               'datatype'        => 'text',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                //            '22018' => [
                //               'table'           => 'glpi_plugin_jamf_mobiledevices',
                //               'field'           => 'lost_location_date',
                //               'name'            => 'Jamf - ' ._x('field', 'Lost mode location date', 'jamf'),
                //               'datatype'        => 'datetime',
                //               'massiveaction'   => false,
                //               'joinparams'      => ['jointype' => 'itemtype_item']
                //            ],
                '22019' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'import_date',
                    'name'          => 'Jamf - ' . _x('field', 'Import date', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
                '22020' => [
                    'table'         => 'glpi_plugin_jamf_devices',
                    'field'         => 'sync_date',
                    'name'          => 'Jamf - ' . _x('field', 'Sync date', 'jamf'),
                    'datatype'      => 'datetime',
                    'massiveaction' => false,
                    'joinparams'    => ['jointype' => 'itemtype_item'],
                ],
            ];
        }
    }

    return $opt;
}

function plugin_jamf_dashboardCards($cards = [])
{
    if (is_null($cards)) {
        $cards = [];
    }
    $cards = array_merge($cards, PluginJamfExtensionAttribute::dashboardCards());
    $cards = array_merge($cards, PluginJamfMobileDevice::dashboardCards());

    return $cards;
}

function plugin_jamf_showJamfInfoForItem(array $params)
{
    $item = $params['item'];
    /** @var PluginJamfAbstractDevice $jamf_class */
    $jamf_class = PluginJamfAbstractDevice::getJamfItemClassForGLPIItem($item::getType(), $item->getID());
    if ($jamf_class !== null) {
        return $jamf_class::showForItem($params);
    }
}

function plugin_jamf_status()
{
    $classic_api_status = PluginJamfAPI::testClassicAPIConnection();
    $pro_api_status     = PluginJamfAPI::testProAPIConnection();

    return [
        'status'      => ($classic_api_status && $pro_api_status) ? StatusChecker::STATUS_OK : StatusChecker::STATUS_PROBLEM,
        'classic_api' => [
            'status' => $classic_api_status ? StatusChecker::STATUS_OK : StatusChecker::STATUS_PROBLEM,
        ],
        'pro_api' => [
            'status' => $pro_api_status ? StatusChecker::STATUS_OK : StatusChecker::STATUS_PROBLEM,
        ],
    ];
}
