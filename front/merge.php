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

use Glpi\Exception\Http\BadRequestHttpException;
use Glpi\DBAL\QueryExpression;
use Glpi\Application\View\TemplateRenderer;
use Glpi\Exception\Http\NotFoundHttpException;

$plugin = new Plugin();
if (!$plugin->isActivated('jamf')) {
    throw new NotFoundHttpException();
}

Session::checkRight(PluginJamfMobileDevice::$rightname, CREATE);
Html::header('Jamf Plugin', '', 'tools', 'PluginJamfMenu', 'import');

global $DB, $CFG_GLPI;

$start = $_GET['start'] ?? 0;

$import      = new PluginJamfImport();
$importcount = countElementsInTable(PluginJamfImport::getTable());
$pending     = iterator_to_array($DB->request([
    'FROM'  => PluginJamfImport::getTable(),
    'START' => $start,
    'LIMIT' => $_SESSION['glpilist_limit'],
]));

// Hide already linked items with a subquery: the dropdown keeps it in session under a short hash,
// instead of inlining every linked id in each row
$conditions = [];
foreach ($pending as &$data) {
    $itemtype = $data['type'];
    if (is_a($itemtype, 'CommonDBTM', true) === false) {
        throw new BadRequestHttpException();
    }

    $conditions[$itemtype] ??= [
        new QueryExpression(
            $DB->quoteName($itemtype::getTable() . '.id') . ' NOT IN (SELECT `items_id` FROM `glpi_plugin_jamf_devices` WHERE `itemtype` = '
            . $DB->quoteValue($itemtype) . ')',
        ),
    ];

    $item     = new $itemtype();
    $jamftype = ('PluginJamf' . $data['jamf_type']);
    $guesses  = $DB->request([
        'SELECT' => ['id'],
        'FROM'   => $itemtype::getTable(),
        'WHERE'  => [
            'OR' => [
                'uuid' => $data['udid'],
                'name' => $data['name'],
            ],
            'is_deleted'  => 0,
            'is_template' => 0,
        ],
        'ORDER' => new QueryExpression("CASE WHEN uuid='" . $data['udid'] . "' THEN 0 ELSE 1 END"),
        'LIMIT' => 1,
    ]);
    $data['guessed_item'] = count($guesses) > 0 ? $guesses->current()['id'] : 0;
}

TemplateRenderer::getInstance()->display('@jamf/merge.html.twig', [
    'pending'     => $pending,
    'total_count' => $importcount,
    'conditions'  => $conditions,
]);
Html::footer();
