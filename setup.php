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

define('PLUGIN_JAMF_VERSION', '3.1.2');
define('PLUGIN_JAMF_MIN_GLPI', '10.0.0');
define('PLUGIN_JAMF_MAX_GLPI', '10.1.0');

function plugin_init_jamf()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['jamf'] = true;

    if (Plugin::isPluginActive('jamf')) {
        $PLUGIN_HOOKS['add_javascript']['jamf'][] = 'js/jamf.js';
        Plugin::registerClass('PluginJamfConfig', ['addtabon' => 'Config']);
        $PLUGIN_HOOKS['post_item_form']['jamf']           = 'plugin_jamf_showJamfInfoForItem';
        $PLUGIN_HOOKS['pre_item_update']['jamf']['Phone'] = ['PluginJamfMobileDevice', 'preUpdatePhone'];
        $PLUGIN_HOOKS['undiscloseConfigValue']['jamf']    = [PluginJamfConfig::class, 'undiscloseConfigValue'];
        Plugin::registerClass('PluginJamfRuleImportCollection', ['rulecollections_types' => true]);
        Plugin::registerClass('PluginJamfProfile', ['addtabon' => ['Profile']]);
        Plugin::registerClass('PluginJamfItem_ExtensionAttribute', ['addtabon' => [
            'Computer',
            'Phone',
        ]]);
        Plugin::registerClass('PluginJamfItem_MDMCommand', ['addtabon' => [
            'Computer',
            'Phone',
        ]]);
        Plugin::registerClass('PluginJamfUser_JSSAccount', ['addtabon' => ['User']]);
        if (Session::haveRight('plugin_jamf_mobiledevice', READ)) {
            $PLUGIN_HOOKS['menu_toadd']['jamf'] = ['tools' => 'PluginJamfMenu'];
        }
        $PLUGIN_HOOKS['post_init']['jamf']  = 'plugin_jamf_postinit';
        $PLUGIN_HOOKS['item_purge']['jamf'] = [
            'Computer' => ['PluginJamfAbstractDevice', 'plugin_jamf_purgeComputer'],
            'Phone'    => ['PluginJamfAbstractDevice', 'plugin_jamf_purgePhone'],
            'Software' => ['PluginJamfSoftware', 'plugin_jamf_purgeSoftware'],
        ];

        // Dashboards
        $PLUGIN_HOOKS['dashboard_cards']['jamf'] = 'plugin_jamf_dashboardCards';

        $PLUGIN_HOOKS['secured_configs']['Jamf'] = ['jsspassword'];
    }
}

function plugin_version_jamf()
{
    return [
        'name'         => _x('plugin_info', 'JAMF Plugin for GLPI', 'jamf'),
        'version'      => PLUGIN_JAMF_VERSION,
        'author'       => "<a href=\"mailto:contact@teclib.com\">Teclib'</a> & Curtis Conard",
        'license'      => 'GPLv2',
        'homepage'     => 'https://github.com/pluginsGLPI/jamf',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_JAMF_MIN_GLPI,
                'max' => PLUGIN_JAMF_MAX_GLPI,
            ],
        ],
    ];
}

function plugin_jamf_check_prerequisites()
{
    if (!method_exists('Plugin', 'checkGlpiVersion')) {
        $version         = preg_replace('/^((\d+\.?)+).*$/', '$1', GLPI_VERSION);
        $matchMinGlpiReq = version_compare($version, PLUGIN_JAMF_MIN_GLPI, '>=');
        $matchMaxGlpiReq = version_compare($version, PLUGIN_JAMF_MAX_GLPI, '<');
        if (!$matchMinGlpiReq || !$matchMaxGlpiReq) {
            echo vsprintf(
                'This plugin requires GLPI >= %1$s and < %2$s.',
                [
                    PLUGIN_JAMF_MIN_GLPI,
                    PLUGIN_JAMF_MAX_GLPI,
                ],
            );

            return false;
        }
    }

    return true;
}

function plugin_jamf_check_config()
{
    return true;
}
