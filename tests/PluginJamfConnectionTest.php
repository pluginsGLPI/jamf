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
 * @copyright Copyright (C) 2014-2023 by Teclib'.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/jamf
 * -------------------------------------------------------------------------
 */

namespace GlpiPlugin\Jamf\Tests;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\ClientTrait;
use PluginJamfConnection;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PluginJamfConnectionTest extends PluginJamfConnection
{
    public function getClient(): ClientInterface
    {
        if ($this->client === null) {
            $this->client = new class implements ClientInterface {
                use ClientTrait;

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    $endpoint = $request->getUri()->getPath();
                    var_dump($endpoint);
                    // remove query parameters
                    $endpoint       = str_contains($endpoint, '?') ? explode('?', $endpoint)[0] : $endpoint;
                    $response_type  = $request->getHeader('Accept')[0] ?? 'application/json';
                    $response_ext   = $response_type === 'application/xml' ? 'xml' : 'json';
                    $mock_file_path = GLPI_ROOT . '/plugins/jamf/tools/samples/' . $endpoint . '.' . $response_ext;

                    return new Response(200, [], file_get_contents($mock_file_path));
                }

                public function request(string $method, $uri, array $options = []): ResponseInterface
                {
                    $request = new Request($method, $uri, $options['headers'] ?? []);

                    return $this->sendRequest($request);
                }

                public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
                {
                    return Create::promiseFor($this->request($method, $uri, $options));
                }
            };
        }

        return $this->client;
    }
}
