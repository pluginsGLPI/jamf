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

namespace GlpiPlugin\Jamf\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PluginJamfConnection;

use function Safe\file_get_contents;

class PluginJamfConnectionTest extends PluginJamfConnection
{
    public function getClient(): Client
    {
        $mock = new MockHandler([
            function ($request, $options) {
                $endpoint = $request->getUri()->getPath();
                $endpoint = str_contains($endpoint, '?') ? explode('?', $endpoint)[0] : $endpoint;

                $response_type  = $request->getHeaderLine('Accept') ?: 'application/json';
                $response_ext   = $response_type === 'application/xml' ? 'xml' : 'json';
                $mock_file_path = GLPI_ROOT . '/plugins/jamf/tools/samples/' . $endpoint . '.' . $response_ext;

                $body = file_get_contents($mock_file_path);

                return new Response(200, [], $body);
            },
        ]);

        $handlerStack = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handlerStack]);

        return $this->client;
    }
}
