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

/**
 * PluginJamfRuleImport class. Represents a rule for importing devices into GLPI.
 * Determines if the import happens or if it is dropped.
 * @since 1.0.0
 */
class PluginJamfRuleImport extends Rule
{
    public static $rightname = 'plugin_jamf_ruleimport';

    public $can_sort         = true;

    public function getTitle()
    {
        return _x('itemtype', 'Device import rules', 'jamf');
    }

    public function maxActionsCount()
    {
        return 1;
    }

    public function getCriterias()
    {
        return ['name' => ['field' => 'name', 'name' => _x('field', 'Name', 'jamf'), 'table' => ''], 'itemtype' => ['field' => 'itemtype', 'name' => _x('field', 'Item type', 'jamf'), 'table' => '', 'allow_condition' => [Rule::PATTERN_IS, Rule::PATTERN_IS_NOT]], 'last_inventory' => ['field' => 'last_inventory', 'name' => _x('field', 'Last inventory', 'jamf'), 'table' => ''], 'managed' => ['field' => 'managed', 'name' => _x('field', 'Managed', 'jamf'), 'type' => 'yesno', 'table' => ''], 'supervised' => ['field' => 'supervised', 'name' => _x('field', 'Supervised', 'jamf'), 'type' => 'yesno', 'table' => '']];
    }

    public function getActions()
    {
        return ['_import' => ['name' => _x('action', 'Import', 'jamf'), 'type' => 'yesno']];
    }

    public function displayAdditionalRuleCondition($condition, $crit, $name, $value, $test = false)
    {
        if (isset($crit['field']) && $crit['field'] === 'itemtype') {
            Dropdown::showFromArray($name, [
                Computer::getType() => Computer::getTypeName(1),
                Phone::getType()    => Phone::getTypeName(1),
            ]);
            return true;
        }

        return false;
    }
}
