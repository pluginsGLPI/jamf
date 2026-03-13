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

use Glpi\Application\View\TemplateRenderer;

include('../../../inc/includes.php');

$plugin = new Plugin();
if (!$plugin->isActivated('jamf')) {
    Html::displayNotFoundError();
}

Html::header('Jamf Plugin', '', 'tools', 'PluginJamfMenu', 'import');

global $CFG_GLPI;

$plugin_dir = Plugin::getWebDir('jamf');
$links      = [];
if (Session::haveRight('plugin_jamf_mobiledevice', CREATE)) {
    $links[] = [
        'name'          => _x('menu', 'Import devices', 'jamf'),
        'url'           => PluginJamfImport::getSearchURL(),
        'description'   => __('Discover the devices in your Jamf Pro instance to ensure data integrity and prevent the creation of unwanted assets.', 'jamf'),
        'action'        => __s('Import', 'jamf'),
        'pics'          => $plugin_dir . '/pics/import.png',
    ];
    $links[] = [
        'name'          => _x('menu', 'Merge existing devices', 'jamf'),
        'url'           => $plugin_dir . '/front/merge.php',
        'description'   => __('Compare imported devices with those already present in GLPI using unique identifiers and facilitate the consolidation.', 'jamf'),
        'action'        => __s('Merge', 'jamf'),
        'pics'          => $plugin_dir . '/pics/merge.png',
    ];
}

TemplateRenderer::getInstance()->display('@jamf/menu.html.twig', [
    'links'         => $links,
    'can_configure' => Session::haveRight('config', UPDATE),
    'config_url'    => Config::getFormURL() . '?forcetab=PluginJamfConfig$1',
]);

Html::footer();
