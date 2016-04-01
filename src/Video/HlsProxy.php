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

class HlsProxy
{
    private $type;

    /**
     * The base URL for the un-watermarked proxy. SafeStream uses this URL to find the un-watermarked files so it can then use them to created watermarked versions
     */
    public $url;

    /**
     * SafeStream encoding requires the segment duration. This segment duration defines the duration for ALL segments in the proxy
     */
    public $segmentDuration;

    /**
     * The segment name format that SafeStream can use to locate each HLS segment in the base URL. Since we do not provide the specific URL to each segment and only provide the base URL to ALL segments
     */
    public $segmentNameFormat;

    /**
     * Specific segment duration overrides
     *
     * The following example overrides the duration of the 3rd segment to 5 seconds
     *
     * <code>
     *
     *     Map<String, Object> overrides - new HaspMap<String, Object>();
     *     overrides.put("3", 5.0);
     *
     * </code>
     */
    public $segmentOverrides;

    /**
     * The total HLS segments in the proxy. Knowing the total number of segments allows SafeStream to locate the segments at the base URL by index.
     */
    public $segmentCount;
}