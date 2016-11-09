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

namespace SafeStream\Watermark;

use SafeStream\Http\SafeStreamHttpClient;
use SafeStream\Http\SafeStreamHttpException;
use SafeStream\Watermark;
use SafeStream\CustomException;

class WatermarkClientException extends CustomException {}
class WatermarkingException extends WatermarkClientException {}

/**
 * Class WatermarkClient
 * @package SafeStream\Watermark
 *
 * The Watermark API provides support for adding a destructive watermark to videos.
 */
class WatermarkClient extends SafeStreamHttpClient
{
    /**
     * The SafeStream REST endpoint for video requests
     */
    private $apiResourcePath = "watermark";

    private $template;

    private $args;

    public function __construct(array $args = [])
    {
        parent::__construct($args);
        $this->args = $args;
    }

    public function template() {
        if(is_null($this->template)) {
            $this->template = new Watermark\Template\TemplateClient($this->args);
        }

        return $this->template;
    }

    /**
     * Watermarks a video
     *
     * @param $videoKey
     *          The unique key for the video to watermark. The key is a property on the video
     *          defined at ingest time. @see http://docs.safestream.com/docs/video
     * @param $watermarkConfiguration
     *          A single configuration object OR an array of configuration object
     *          @see WatermarkConfiguration
     * @param int $timeout The length of time to wait for the watermarking to complete. Most
     *          videos will complete in less than 60 seconds.
     *
     *          A value greater than 0 will cause the function to wait for the watermarking to
     *          complete until the timeout is reached. Otherwise the function will return
     *          immediately
     * @return mixed
     * @throws WatermarkClientException
     * @throws WatermarkConfigurationException
     * @throws WatermarkingException
     */
    public function create($videoKey, $watermarkConfiguration, $timeout = 90000) {
        $this->validateVideoKey($videoKey);
        $this->validateWatermarkConfiguration($watermarkConfiguration);
        $this->validateTimeout($timeout);

        $config = is_array($watermarkConfiguration) ? $watermarkConfiguration : array($watermarkConfiguration);
        $payload = array("key" => $videoKey, "settings" => $config);

        return $this->createAPI($payload, $timeout);
    }

    /**
     * Watermarks a video from an existing watermark template.
     * For more on watermark templates @see Watermark\Template\Template
     *
     * @param $videoKey
     *          The unique key for the video to watermark. The key is a property on the video
     *          defined at ingest time. @see http://docs.safestream.com/docs/video
     * @param $templateId
     * @param $templateMapping
     *          Key/Value pairs that hydrate the template
     * @param int $timeout The length of time to wait for the watermarking to complete. Most
     *          videos will complete in less than 60 seconds.
     * @return mixed
     * @throws WatermarkClientException
     * @throws WatermarkingException
     */
    public function createFromTemplate($videoKey, $templateId, $templateMapping, $timeout = 90000) {
        $payload = array("key" => $videoKey, "settingsTemplateMapping" => array( "id" => $templateId, "mappings" => $templateMapping ));
        return $this->createAPI($payload, $timeout);
    }

    private function createAPI($payload, $timeout = 90000) {
        $watermarkResult = $this->post($this->apiResourcePath, $payload);

        // If we have a timeout then we'll wait until the watermarking has completed before
        // returning
        try {
            $timeout = !is_null($timeout) ? $timeout : 90000; // Default to 3 hours

            if($timeout > 0) {
                $startTime = round(microtime(true) * 1000);
                $ready = false;
                while (!$ready && (round(microtime(true) * 1000) - $startTime) < $timeout) {
                    $pollResult = $this->request($watermarkResult->href, null, "GET", True);

                    if ($pollResult->status == "READY") {
                        $ready = true;
                        return $pollResult;
                    }

                    sleep(3);
                }

                throw new WatermarkingException("The video has not completed watermarking before the
            timeout");
            } else {
                return $watermarkResult;
            }
        } catch(SafeStreamHttpException $e) {
            throw new WatermarkClientException($e);
        }
    }

    private function validateVideoKey($videoKey) {
        if(is_null($videoKey) || !is_string($videoKey)) {
            throw new WatermarkClientException("The video key must be a string");
        }
    }

    private function validateWatermarkConfiguration($watermarkConfiguration) {
        $watermarkConfigurationExceptionMessage = "The watermark configuration parameter must
             wither be a WatermarkConfiguration or an array of WatermarkConfiguration";

        if(!is_array($watermarkConfiguration) && get_class($watermarkConfiguration) != WatermarkConfiguration::class) {
            throw new WatermarkConfigurationException($watermarkConfigurationExceptionMessage . "
             instead found " . get_class($watermarkConfiguration));
        }

        if(is_array($watermarkConfiguration)) {
            foreach ($watermarkConfiguration as $config) {
                if(get_class($config) != WatermarkConfiguration::class) {
                    throw new WatermarkConfigurationException
                    ($watermarkConfigurationExceptionMessage . " instead found " . get_class($config));
                }
            }
        }
    }

    private function validateTimeout($timeout) {
        if(!is_null($timeout) && !is_numeric($timeout)) {
            throw new WatermarkConfigurationException("Timeout must be an Epoch time in the future");
        }
    }
}
