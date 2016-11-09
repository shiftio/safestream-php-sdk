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
class SafeStreamHttpResponseException extends SafeStreamHttpException {}
class SafeStreamHttpAuthException extends SafeStreamHttpResponseException {}
class SafeStreamHttpBadRequestException extends SafeStreamHttpResponseException {}
class SafeStreamHttpThrottleException extends SafeStreamHttpResponseException {}
class SafeStreamHttpConflictException extends SafeStreamHttpResponseException {}

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
     * SafeStream Client ID used to identify the user authenticating to the API
     */
    private $clientId;

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
        $this->protocol = isset($args['protocol']) ? $args['protocol'] : "https";
        $this->hostName = isset($args['hostName']) ? $args['hostName'] : "api.safestream.com";
        $this->version = isset($args['version']) ? $args['version'] : "1.0";
        $this->apiKey = isset($args['apiKey']) ? $args['apiKey'] : "apiKey";
        $this->clientId = isset($args['clientId']) ? $args['clientId'] : "clientId";
        $this->client = new GuzzleHttp\Client([ 'base_uri' => $this->getRootUrl() ]);
        $this->getAuthToken();
    }

    /**
     * Gets a given SafeStream resource path
     * @param string $path:The SafeStream resource path
     * @return mixed The JSON decoded response
     */
    public function get($path) {
        return $this->request($path, null, "GET", True);
    }

    /**
     * @param string $path: SafeStream resource path
     * @param $body: Request payload
     * @return mixed The JSON decoded response
     */
    public function post($path, $body) {
        return $this->request($path, $body, "POST", True);
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
    public function request($path, $body, $method, $retry = False)
    {
        $requestOptions = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'     => 'Bearer ' . $this->authToken
        ]];

        if(!is_null($body)) {
            $body = (object) array_filter((array) $body); // Remove nulls
            $requestOptions['body'] = json_encode($body);
        }


        try {
            $response = $this->client->request($method, $path, $requestOptions);
            return json_decode($response->getBody());
        } catch(GuzzleHttp\Exception\RequestException $e) {
            $response_code = $e->getResponse()->getStatusCode();

            if($response_code == 401 && $retry) {
                $this->getAuthToken();
                $this->request($path, $body, $method, False);
            } else {
                $this->handleExceptionResult($e);
            }
        }
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
        try {
            $response = $this->client->request('GET', "authenticate/accessToken", ['headers' => [
                'x-api-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey
            ]]);

            $this->authToken = json_decode($response->getBody())->token;

            return $this->authToken;
        } catch (GuzzleHttp\Exception\RequestException $e) {
            $this->handleExceptionResult($e);
        }
    }

    /**
     * @param GuzzleHttp\Exception\RequestException $exception
     * @throws SafeStreamHttpAuthException
     * @throws SafeStreamHttpBadRequestException
     * @throws SafeStreamHttpConflictException
     * @throws SafeStreamHttpException
     * @throws SafeStreamHttpThrottleException
     */
    private function handleExceptionResult(GuzzleHttp\Exception\RequestException $exception) {
        $response_code = $exception->getResponse()->getStatusCode();

        if($response_code == 400) {
            throw new SafeStreamHttpBadRequestException($exception);
        }

        if($response_code == 401 || $response_code == 403) {
            throw new SafeStreamHttpAuthException($exception);
        }

        if($response_code == 409 || $response_code == 409) {
            throw new SafeStreamHttpConflictException($exception);
        }

        if($response_code == 420) {
            throw new SafeStreamHttpThrottleException($exception);
        }

        throw new SafeStreamHttpException($exception);
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
