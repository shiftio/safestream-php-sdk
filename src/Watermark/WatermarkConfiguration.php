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

class WatermarkConfigurationException extends CustomException {}

/**
 * Class WatermarkConfiguration
 * @package SafeStream\Watermark
 *
 * Defines the settings for a given watermark. At minimum this configuration requires "content",
 * a string that will be watermarked onto the video.
 */
class WatermarkConfiguration {

    /**
     * The text string to watermark onto the video
     */
    public $content = "";

    /**
     * Currently <code>TEXT</code> is support.
     */
    public $type = WatermarkType::TEXT;

    /**
     * Specifies the horizontal anchor point on the watermark.
     *
     * A value of LEFT will anchor the watermark on it's right most point
     * A value of RIGHT will anchor the watermark on it's left most pixel
     * A value of CENTER will anchor the watermark in it's center most pixel
     */
    public $align = WatermarkHorizontalAlignment::CENTER;

    /**
     * Specifies the vertical anchor point on the watermark.
     *
     * A value of TOP will anchor the watermark on it's top most point
     * A value of MIDDLE will anchor the watermark on it's middle most pixel
     * A value of BOTTOM will anchor the watermark in it's bottom most pixel
     */
    public $verticalAlign = WatermarkVerticalAlignment::MIDDLE;


    /**
     * The relative x position of the anchor. The position is relative to the width of the video. So, a video with a width of 1080 and an x value of .5 will put the anchor point at 540 pixels. The anchor position is defined by the horizontal and vertical alignment.
     */
    public $x = 0.5;

    /**
     * The relative y position of the anchor. The position is relative to the height of the video. So, a video with a height of 720 and an y value of .5 will put the anchor point at 360 pixels. The anchor position is defined by the horizontal and vertical alignment.
     */
    public $y = 0.5;

    /**
     * Size of the watermark text relative to the height of the video. For example, a video with a height of 720 and a font size of .05 will produce a watermark with a text font size of 36
     */
    public $fontSize = 0.05;

    /**
     * Values from 0 (totally transparent) to 1 (totally opaque)
     */
    public $fontOpacity = 0.3;

    /**
     * Hex value of font color (ex 0xffffff)
     */
    public $fontColor = "0xffffff";

    /**
     * Opacity of the drop shadow of the watermark text. 0 (totally transparent) to 1 (totally opaque)
     */
    public $shadowOpacity = 0.1;

    /**
     * Hex value of watermark text drop shadow color (ex 0xffffff)
     */
    public $shadowColor = "0x000000";

    public $shadowOffsetX = 0.08;
    public $shadowOffsetY = 0.08;

    public function __construct(array $args = [])
    {
        $numberBetweenExceptionMessage = "";//"Property '%s' must be between %s and %s";
        $notAStringExceptionMessage = "";//"Property '%s' must be a string";

        if(isset($args['content'])) {
            if(!is_string($args['content']) || strlen($args['content']) == 0) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'type'));
            }

            $this->content = $args['content'];
        }

        if(isset($args['type'])) {
            if(!is_string($args['type'])) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'type'));
            }

            $this->type = $args['type'];
        }

        if(isset($args['x'])) {
            if(!is_numeric($args['x']) && !$this->isBetweenZeroAndOne($args['x'])) {
                throw new WatermarkConfigurationException(sprintf($numberBetweenExceptionMessage, 'x',
                    0, 1));
            }

            $this->x = $args['x'];
        }

        if(isset($args['y'])) {
            if(!is_numeric($args['y']) && !$this->isBetweenZeroAndOne($args['y'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'y', 0,
                    1);
            }

            $this->y = $args['y'];
        }

        if(isset($args['fontSize'])) {
            if(!is_numeric($args['fontSize']) && !$this->isBetweenZeroAndOne($args['fontSize'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'fontSize', 0,
                    1);
            }

            $this->fontSize = $args['fontSize'];
        }

        if(isset($args['fontOpacity'])) {
            if(!is_numeric($args['fontOpacity']) && !$this->isBetweenZeroAndOne($args['fontOpacity'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'fontOpacity', 0,
                    1);
            }

            $this->fontOpacity = $args['fontOpacity'];
        }

        if(isset($args['fontColor'])) {
            if(!is_string($args['fontColor'])) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'fontColor'));
            }

            $this->fontColor = $args['fontColor'];
        }

        if(isset($args['shadowOpacity'])) {
            if(!is_numeric($args['shadowOpacity']) && !$this->isBetweenZeroAndOne($args['shadowOpacity'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'shadowOpacity', 0,
                    1);
            }

            $this->shadowOpacity = $args['shadowOpacity'];
        }

        if(isset($args['shadowColor'])) {
            if(!is_string($args['shadowColor'])) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'shadowColor'));
            }

            $this->shadowColor = $args['shadowColor'];
        }

        if(isset($args['shadowOffsetX'])) {
            if(!is_numeric($args['shadowOffsetX']) && !$this->isBetweenZeroAndOne($args['shadowOffsetX'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'shadowOffsetX', 0,
                    1);
            }

            $this->shadowOffsetX = $args['shadowOffsetX'];
        }

        if(isset($args['shadowOpacity'])) {
            if(!is_numeric($args['shadowOffsetY']) && !$this->isBetweenZeroAndOne($args['shadowOffsetY'])) {
                throw new WatermarkConfigurationException($numberBetweenExceptionMessage, 'shadowOffsetY', 0,
                    1);
            }

            $this->shadowOffsetY = $args['shadowOffsetY'];
        }

        if(isset($args['horizontalAlignment'])) {
            if(!is_string($args['horizontalAlignment'])) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'horizontalAlignment'));
            }

            $this->align = $args['horizontalAlignment'];
        }

        if(isset($args['verticalAlignment'])) {
            if(!is_string($args['verticalAlignment'])) {
                throw new WatermarkConfigurationException(sprintf($notAStringExceptionMessage, 'verticalAlignment'));
            }

            $this->verticalAlign = $args['verticalAlignment'];
        }
    }

    public function withContent($content) {
        $this->content = $content;
        return $this;
    }

    public function withType($type) {
        $this->type = $type;
        return $this;
    }

    public function withHorizontalAlignment($horizontalAlignment) {
        $this->align = $horizontalAlignment;
        return $this;
    }

    public function withVerticalAlignment($verticalAlignment) {
        $this->verticalAlign = $verticalAlignment;
        return $this;
    }

    public function withX($x) {
        $this->x = $x;
        return $this;
    }

    public function withY($y) {
        $this->y = $y;
        return $this;
    }

    public function withFontSize($fontSize) {
        $this->fontSize = $fontSize;
        return $this;
    }

    public function withFontOpacity($fontOpacity) {
        $this->fontOpacity = $fontOpacity;
        return $this;
    }

    public function withFontColor($fontColor) {
        $this->fontColor = $fontColor;
        return $this;
    }

    public function withShadowOpacity($shadowOpacity) {
        $this->shadowOpacity = $shadowOpacity;
        return $this;
    }

    public function withShadowColor($shadowColor) {
        $this->shadowColor = $shadowColor;
        return $this;
    }

    public function withShadowOffsetX($shadowOffsetX) {
        $this->shadowOffsetX = $shadowOffsetX;
        return $this;
    }

    public function withShadowOffsetY($shadowOffsetY) {
        $this->shadowOffsetY = $shadowOffsetY;
        return $this;
    }

    private function isBetweenZeroAndOne($val) {
        if(!$val >= 0 && !$val <= 1) {
            return false;
        }

        return true;
    }
}