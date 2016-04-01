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

namespace SafeStream\Configuration\Storage\S3;

class S3StorageConfiguration extends \StorageConfiguration
{
    public function __construct()
    {
        parent::__construct(\StorageConfigurationType::AWS_S3, array());
    }

    /**
     * Fluent setter for S3 bucket name. This is REQUIRED for using S3 as a storage service
     * @param bucketName
     * @return this
     */
    public function withBucket($bucketName) {
        $this->properties["bucketName"] = $bucketName;
        return $this;
    }

    /**
     * Fluent setter for S3 region. This is REQUIRED for using S3 as a storage service
     * @param region
     * @return this
     */
    public function withRegion($region) {
        $this->properties["region"] = $region;
        return $this;
    }

    /**
     * Fluent setter for S3 accessKey. This is REQUIRED for using S3 as a storage service
     * @param accessKey
     * @return this
     */
    public function withAccessKey($accessKey) {
        $this->properties["accessKey"] = $accessKey;
        return $this;
    }

    /**
     * Fluent setter for S3 secretKey. This is REQUIRED for using S3 as a storage service
     * @param secretKey
     * @return this
     */
    public function withSecretKey($secretKey) {
        $this->properties["secretKey"] = $secretKey;
        return $this;
    }
}