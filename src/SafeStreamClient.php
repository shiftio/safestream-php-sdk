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

namespace SafeStream;

use SafeStream\Video\VideoClient;
use SafeStream\Watermark\WatermarkClient;

class SafeStreamClientException extends CustomException {}

/**
 * Class SafeStreamClient
 * @package SafeStream
 *
 * Wraps SafeStream clients, creates a single interface to everything in SafeStream.
 *
 */
class SafeStreamClient
{
    const VERSION = '1.2.1';

    public $args;
    private $watermark;
    private $video;

    public function __construct(array $args = [])
    {
        if(!isset($args['apiKey'])) {
            throw new SafeStreamClientException("An API Key is require to create a
            SafeStreamClient");
        }

        $this->args = $args;
    }

    public function watermark() {
        if(is_null($this->watermark)) {
            $this->watermark = new WatermarkClient($this->args);
        }

        return $this->watermark;
    }

    public function video() {
        if(is_null($this->video)) {
            $this->video = new VideoClient($this->args);
        }

        return $this->video;
    }

}
