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

use SafeStream\CustomException;

class AnimationException extends CustomException {}


/**
 * SafeStream supports the movement of watermarks from their origin location
 * (defined as x and y in WatermarkConfiguration) to an end location over a period of time.
 *
 * Any time an animation is present in your Watermark configuration, it will move according
 * to the animation properties defined below..
 *
 * Class Animation
 * @package SafeStream\Watermark
 */
class Animation
{
    public $type = "MOVE";

    //The relative x coordinate as a percentage of the video width.
    public $to_x = 1;

    //The relative y coordinate as a percentage of the video height.
    public $to_y = 1;

    // The start time of the animation in seconds
    public $startTime = 0.0;

    // The end time of the animation in seconds
    public $endTime = 0.0;

    private $numberBetweenExceptionMessage = "Property '%s' must be between %s and %s";

    public function __construct(array $args = [])
    {
        if(isset($args['to_x'])) $this->endAtXPosition($args['to_x']);
        if(isset($args['to_y'])) $this->endAtYPosition($args['to_y']);
        if(isset($args['startTime'])) $this->withStartTime($args['startTime']);
        if(isset($args['endTime'])) $this->withEndTime($args['endTime']);
    }

    public function endAtXPosition($x) {
        if(isset($x)) {
            if(!is_numeric($x) || !$this->isBetweenZeroAndOne($x)) {
                throw new AnimationException(sprintf($this->numberBetweenExceptionMessage, "x", 0, 1));
            }

            $this->to_x = $x;
        }

        return $this;
    }

    public function endAtYPosition($y) {
        if(isset($y)) {
            if(!is_numeric($y) || !$this->isBetweenZeroAndOne($y)) {
                throw new AnimationException(sprintf($this->numberBetweenExceptionMessage, "y", 0, 1));
            }

            $this->to_y = $y;
        }

        return $this;
    }

    public function withStartTime($startTime) {
        if(isset($startTime)) {
            if(!is_numeric($startTime) || $startTime < 0) {
                throw new AnimationException("The start time must be greater than zero");
            }

            $this->startTime = $startTime;
        }

        return $this;
    }

    public function withEndTime($endTime) {
        if(isset($endTime)) {
            if(!is_numeric($endTime)) {
                throw new AnimationException("The send time must be a number");
            }

            $this->endTime = $endTime;
        }

        return $this;
    }

    private function isBetweenZeroAndOne($val) {
        if($val < 0 || $val > 1) {
            return false;
        }

        return true;
    }
}