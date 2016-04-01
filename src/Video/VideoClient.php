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

namespace SafeStream\Video;

use SafeStream\Http\SafeStreamHttpClient;
use SafeStream\CustomException;
use SafeStream\Http\SafeStreamHttpException;

class VideoClientException extends CustomException {}

/**
 * Class VideoClient
 * @package SafeStream\Video
 *
 * The Video API provides support for ingesting and managing videos within SafeStream.
 */
class VideoClient extends SafeStreamHttpClient
{
    /**
     * The SafeStream REST endpoint for video requests
     */
    private $apiResourcePath = "videos";

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
        parent::__construct($args);
    }

    public function createFromSourceUrl($sourceUrl, $waitForIngest = 0) {
        $video = new Video();
        $video->withSourceUrl($sourceUrl);

        return $this->create($video, $waitForIngest);
    }

    /**
     * Creates a new video allowing a specific timeout while waiting for the video to downloaded
     * and encoded. If not timeout is set then the function will return immediately, before the
     * ingest is complete. The video cannot be watermarked until it has been ingested at which
     * point the video status will be INGESTED.
     *
     * This will block until either the video is ingested OR the timeout is reached.
     *
     * @param Video $video
     * @param int $waitForIngest: Time in millis to wait for the video to be ingested
     * @return mixed: The newly created video
     * @throws VideoClientException
     */
    public function create(Video $video, $waitForIngest = 0) {
        if(is_null($video)) {
            throw new VideoClientException("No video provided");
        }

        if(is_null($video->sourceUrl) || !filter_var($video->sourceUrl, FILTER_VALIDATE_URL)) {
            throw new VideoClientException("Invalid source URL");
        }

        $ingestedStatus = "INGESTED";

        try {
            // Make the request to the SafeStream REST API
            $videoResponse = $this->post($this->apiResourcePath, $video);

            // Wait for the video to be ingested before returning
            // TODO: Have the ingest wait be on a separate thread
            if($waitForIngest > 0 && !$ingestedStatus == $videoResponse->status) {
                $startTime = round(microtime(true) * 1000);
                $ingested = false;
                while(!$ingested && (round(microtime(true) * 1000) - $startTime) < $waitForIngest) {
                    $test = $this->find($video->key);
                    if($ingestedStatus == $videoResponse->status) {
                        return $test;
                    }

                    sleep(3);
                }

                throw new VideoClientException("Timeout reached waiting for video to be ingested");

            } else {
                return $videoResponse;
            }
        } catch(SafeStreamHttpException $e) {
            throw new VideoClientException($e);
        }
    }

    /**
     * Gets an existing video by it's key.
     *
     * @param $videoKey: If no key was passed in when creating the video then the key will be the
     * source URL of the video
     * @return mixed: An existing video {@link Video}
     * @throws VideoClientException
     */
    public function find($videoKey) {
        if(is_null($videoKey) || empty($videoKey)) {
            throw new VideoClientException("A key is needed to fnd a video");
        }

        try {
            // Request the video from the SafeStream REST API
            $videos = $this->get(sprintf("%s?key=%s", $this->apiResourcePath, $videoKey));
            
            return $videos[0];
        } catch (SafeStreamHttpException $e) {
            throw new VideoClientException($e);
        }
    }
}