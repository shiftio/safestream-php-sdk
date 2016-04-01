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


class Video
{
    /**
     * The unique identifier for the video
     */
    public $id;

    /**
     * Videos exist with a specific context. This will typically be the account ID
     */
    public $scope;

    /**
     * This can be an external key of any string value. If no value is given when the video is created then the key will be the source URL.
     */
    public $key;

    /**
     * An optional descriptive name for a video
     */
    public $name;

    /**
     * The URL where the video source exists at the time of creating this video. Currently, http and https URLs are supported
     */
    public $sourceUrl;

    /**
     * Target bit rate in kilobits. For example to create a 4Mb proxy this value would be 4000k.
     */
    public $targetBitRate;

    /**
     * A list of tags that identify characterize this video
     */
    public $tags;

    /**
     * If we should use signed URLs for access tto he watermarked segments and M3U8 of this videos watermarked versions
     */
    public $allowHmacAuth = true;

    /**
     * If we should encrypt the watermarked segments of this video at rest
     */
    public $encrypt = true;

    /**
     * The unwatermarked HLS proxies for this video.
     */
    public $proxies;

    /**
     * An optional configuration that overrides default account configurations and system configurations for storing this video and it's watermarked proxies
     */
    public $config;

    /**
     * The ingest status of this video <code>PENDING, INGESTED</code>
     *
     * Videos can only be watermarked that are in the <code>INGESTED</code> status
     */
    public $status;

    /**
     * Epoch timestamp of the creation of this video
     */
    public $created;

    /**
     * ID of the user who created this video
     */
    public $createdBy;

    /**
     * Fluent setter for the property key
     *
     * @param $key
     * @return $this
     */
    public function withKey($key) {
        $this->key = $key;
        return $this;
    }

    /**
     * Fluent setter for the property name
     *
     * @param $name
     * @return $this
     */
    public function withName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Fluent setter for the property sourceUrl
     *
     * @param $sourceUrl
     * @return $this
     */
    public function withSourceUrl($sourceUrl) {
        $this->sourceUrl = $sourceUrl;
        return $this;
    }

    /**
     * Fluent setter for the property tags
     *
     * @param $tag
     * @return $this
     */
    public function withTag($tag) {
        if (is_null($this->tags)) {
            $this->tags = [];
        }

        array_push($this->tags, $tag);
        return $this;
    }

    /**
     * Fluent setter for the property tags
     *
     * @param $tags
     * @return $this
     */
    public function withTags($tags) {
        if (is_null($this->tags)) {
            $this->tags = [];
        }

        foreach ($tags as $tag) {
            array_push($this->tags, $tags);
        }

        return $this;
    }

    /**
     * Fluent setter for the property allowHmacAuth
     *
     * @return $this
     */
    public function withHMACAuth() {
        $this->allowHmacAuth = true;
        return $this;
    }

    /**
     * Fluent setter for the property allowHmacAuth
     *
     * @return $this
     */
    public function withoutHMACAuth() {
        $this->allowHmacAuth = false;
        return $this;
    }

    /**
     * Fluent setter for the property encrypt
     *
     * @return $this
     */
    public function withEncryption() {
        $this->encrypt = true;
        return $this;
    }

    /**
     * Fluent setter for the property encrypt
     *
     * @return $this
     */
    public function withoutEncryption() {
        $this->encrypt = false;
        return $this;
    }

    /**
     * Fluent setter for the property proxies
     *
     * @param HlsProxy $hlsProxy
     * @return $this
     */
    public function withExistingProxy(HlsProxy $hlsProxy) {
        if(is_null($this->proxies)) {
            $this->proxies = [];
        }

        array_push($this->proxies, $hlsProxy);

        return $this;
    }

    public function withConfig(VideoConfiguration $configuration) {
        $this->config = $configuration;
    }
}