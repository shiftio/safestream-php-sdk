<?php

/*
 * MIT License
 *
 * Copyright (c) 2016 MediaSilo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace SafeStream\Http;

use Psr\Http\Message\ResponseInterface;
use SafeStream\CustomException;
use GuzzleHttp;

class SafeStreamHttpException extends CustomException {}
class SafeStreamHttpAuthException extends SafeStreamHttpException {}
class SafeStreamHttpBadRequestException extends SafeStreamHttpException {}
class SafeStreamHttpThrottleException extends SafeStreamHttpException {}

class SafeStreamHttpClient
{
    /**
     * HTTP, HTTPS
     */
    private $protocol;

    /**
     * SafeStream api hostname
     */
    private $hostName;

    /**
     * SafeStream API version
     */
    private $version;

    /**
     * SafeStream API key. This is required in order to retrieve an auth token and make ANY subsequent requests to the SafeStream API
     */
    private $apiKey;

    private static $authToken;

    /**
     * Constructs a new SDK object with an associative array of default
     * client settings.
     *
     * @param array $args
     *
     * @throws \InvalidArgumentException
     * @see SafeStream\SafeStreamClient::__construct for a list of available options.
     */
    public function __construct(array $args = [])
    {
        $this->protocol = isset($args['protocol']) ? $args['protocol'] : "http";
        $this->hostName = isset($args['hostName']) ? $args['hostName'] : "api.safestream.com";
        $this->version = isset($args['version']) ? $args['version'] : "0.1";
        $this->apiKey = isset($args['apiKey']) ? $args['apiKey'] : "apiKey";
        $this->client = new GuzzleHttp\Client([ 'base_uri' => $this->getRootUrl() ]);
    }

    /**
     * Gets a given SafeStream resource path
     * @param string $path:The SafeStream resource path
     * @return mixed The JSON decoded response
     */
    public function get($path) {
        return $this->request($path, null, "GET");
    }

    /**
     * @param string $path: SafeStream resource path
     * @param $body: Request payload
     * @return mixed The JSON decoded response
     */
    public function post($path, $body) {
        return $this->request($path, $body, "POST");
    }

    /**
     * @param string $path: The SafeStream resource path
     * @param $body: The request payload
     * @param string $method: HTTP method
     * @return mixed: The JSON decoded response
     * @throws SafeStreamHttpAuthException
     * @throws SafeStreamHttpBadRequestException
     * @throws SafeStreamHttpException
     * @throws SafeStreamHttpThrottleException
     */
    public function request($path, $body, $method)
    {
        $requestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'     => 'Bearer ' . $this->getAuthToken()
        ]];

        if(!is_null($body)) {
            $requestOptions['body'] = json_encode($body);
        }

        $response = $this->client->request($method, $path, $requestOptions);

        return $this->handleResult($response);
    }

    /**
     * Gets an authorization token using the clients API key. Most requests to the SafeStream API
     * require an authorization token.
     *
     * @return mixed: The JSON decoded response
     * @throws SafeStreamHttpAuthException
     * @throws SafeStreamHttpBadRequestException
     * @throws SafeStreamHttpException
     * @throws SafeStreamHttpThrottleException
     */
    public function getAuthToken() {
        $response = $this->client->request('POST', "token", ['headers' => [
            'Content-Type' => 'application/json',
            'x-api-key'    => $this->apiKey
        ]]);

        return $this->handleResult($response)->token;
    }

    /**
     * HTTP response handler
     * @param ResponseInterface $response
     * @return mixed The JSON decoded response
     * @throws SafeStreamHttpAuthException
     * @throws SafeStreamHttpBadRequestException
     * @throws SafeStreamHttpException
     * @throws SafeStreamHttpThrottleException
     */
    private function handleResult(ResponseInterface $response) {
        $response_code = $response->getStatusCode();
        if($response_code >= 400) {
            if($response_code == 400) {
                throw new SafeStreamHttpBadRequestException($response->getBody());
            }

            if($response_code == 401 || $response_code == 403) {
                throw new SafeStreamHttpAuthException($response->getBody());
            }

            if($response_code == 420) {
                throw new SafeStreamHttpThrottleException($response->getBody());
            }

            throw new SafeStreamHttpException($response->getBody());
        } else {
            return json_decode($response->getBody());
        }
    }

    /**
     * Constructs the root URL for the SafeStream REST API using the clients arguments
     *
     * @return string
     */
    private function getRootUrl()
    {
        return sprintf('%s://%s/%s/', $this->protocol, $this->hostName, $this->version);
    }

    private function getResourceUrl($resource) {
        return sprintf("%s%s", $this->getRootUrl(), $resource);
    }
}