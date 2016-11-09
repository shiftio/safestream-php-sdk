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

    public $animation;

    private $numberBetweenExceptionMessage = "";//"Property '%s' must be between %s and %s";
    private $notAStringExceptionMessage = "";//"Property '%s' must be a string";

    public function __construct(array $args = [])
    {
        if(isset($args['content'])) $this->withContent($args['content']);
        if(isset($args['type'])) $this->withType($args['type']);
        if(isset($args['x'])) $this->withX($args['x']);
        if(isset($args['y'])) $this->withY($args['y']);
        if(isset($args['fontSize'])) $this->withFontSize($args['fontSize']);
        if(isset($args['fontOpacity'])) $this->withFontOpacity($args['fontOpacity']);
        if(isset($args['fontColor'])) $this->withFontColor($args['fontColor']);
        if(isset($args['shadowOpacity'])) $this->withShadowOpacity($args['shadowOpacity']);
        if(isset($args['shadowColor'])) $this->withShadowColor($args['shadowColor']);
        if(isset($args['shadowOffsetX'])) $this->withShadowOffsetX($args['shadowOffsetX']);
        if(isset($args['shadowOffsetY'])) $this->withShadowOffsetY($args['shadowOffsetY']);
        if(isset($args['horizontalAlignment'])) $this->withHorizontalAlignment($args['horizontalAlignment']);
        if(isset($args['verticalAlignment'])) $this->withVerticalAlignment($args['verticalAlignment']);
        if(isset($args['animation'])) $this->withAnimation($args['animation']);
    }

    public function withContent($content) {
        if(isset($content)) {
            if(!is_string($content) || strlen($content) == 0) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'content'));
            }

            $this->content = $content;
        }

        return $this;
    }

    public function withType($type) {
        if(isset($type)) {
            if(!is_string($type)) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'type'));
            }

            if(strtoupper($type) != 'TEXT') {
                throw new WatermarkConfigurationException("Invalid value for type " . $type);
            }

            $this->type = strtoupper($type);
        }

        return $this;
    }

    public function withHorizontalAlignment($horizontalAlignment) {
        if(isset($horizontalAlignment)) {
            if(!is_string($horizontalAlignment)) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'horizontalAlignment'));
            }

            if($horizontalAlignment != WatermarkHorizontalAlignment::LEFT && $horizontalAlignment != WatermarkHorizontalAlignment::CENTER && $horizontalAlignment != WatermarkHorizontalAlignment::RIGHT) {
                throw new WatermarkConfigurationException("Invalid value for horizontalAlignment " . $horizontalAlignment);
            }

            $this->align = strtoupper($horizontalAlignment);
        }

        return $this;
    }

    public function withVerticalAlignment($verticalAlignment) {
        if(isset($verticalAlignment)) {
            if(!is_string($verticalAlignment)) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'verticalAlignment'));
            }

            if($verticalAlignment != WatermarkVerticalAlignment::TOP && $verticalAlignment != WatermarkVerticalAlignment::MIDDLE && $verticalAlignment != WatermarkVerticalAlignment::BOTTOM) {
                throw new WatermarkConfigurationException("Invalid value for verticalAlignment " . $verticalAlignment);
            }

            $this->verticalAlign = strtoupper($verticalAlignment);
        }

        return $this;
    }

    public function withX($x) {
        if(isset($x)) {
            if(!is_numeric($x) && !$this->isBetweenZeroAndOne($x)) {
                throw new WatermarkConfigurationException(sprintf($this->numberBetweenExceptionMessage, 'x',
                    0, 1));
            }

            $this->x = $x;
        }

        return $this;
    }

    public function withY($y) {
        if(isset($y)) {
            if(!is_numeric($y) && !$this->isBetweenZeroAndOne($y)) {
                throw new WatermarkConfigurationException(sprintf($this->numberBetweenExceptionMessage, 'y',
                    0, 1));
            }

            $this->y = $y;
        }

        return $this;
    }

    public function withFontSize($fontSize) {
        if(isset($fontSize)) {
            if(!is_numeric($fontSize) && !$this->isBetweenZeroAndOne($fontSize)) {
                throw new WatermarkConfigurationException($this->numberBetweenExceptionMessage, 'fontSize', 0,
                    1);
            }

            $this->fontSize = $fontSize;
        }

        return $this;
    }

    public function withFontOpacity($fontOpacity) {
        if(isset($fontOpacity)) {
            if(!is_numeric($fontOpacity) && !$this->isBetweenZeroAndOne($fontOpacity)) {
                throw new WatermarkConfigurationException($this->numberBetweenExceptionMessage, 'fontOpacity', 0,
                    1);
            }

            $this->fontOpacity = $fontOpacity;
        }

        return $this;
    }

    public function withFontColor($fontColor) {
        if(isset($fontColor)) {
            if(!is_string($fontColor)) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'fontColor'));
            }

            $this->fontColor = $fontColor;
        }

        return $this;
    }

    public function withShadowOpacity($shadowOpacity) {
        if(isset($shadowOpacity)) {
            if(!is_numeric($shadowOpacity) && !$this->isBetweenZeroAndOne($shadowOpacity)) {
                throw new WatermarkConfigurationException($this->numberBetweenExceptionMessage, 'shadowOpacity', 0,
                    1);
            }

            $this->shadowOpacity = $shadowOpacity;
        }

        return $this;
    }

    public function withShadowColor($shadowColor) {
        if(isset($shadowColor)) {
            if(!is_string($shadowColor)) {
                throw new WatermarkConfigurationException(sprintf($this->notAStringExceptionMessage, 'shadowColor'));
            }

            $this->shadowColor = $shadowColor;
        }

        return $this;
    }

    public function withShadowOffsetX($shadowOffsetX) {
        if(isset($shadowOffsetX)) {
            if(!is_numeric($shadowOffsetX) && !$this->isBetweenZeroAndOne($shadowOffsetX)) {
                throw new WatermarkConfigurationException($this->numberBetweenExceptionMessage, 'shadowOffsetX', 0,
                    1);
            }

            $this->shadowOffsetX = $shadowOffsetX;
        }

        return $this;
    }

    public function withShadowOffsetY($shadowOffsetY) {
        if(isset($shadowOffsetY)) {
            if(!is_numeric($shadowOffsetY) && !$this->isBetweenZeroAndOne($shadowOffsetY)) {
                throw new WatermarkConfigurationException($this->numberBetweenExceptionMessage, 'shadowOffsetY', 0,
                    1);
            }

            $this->shadowOffsetY = $shadowOffsetY;
        }

        return $this;
    }

    public function withAnimation($animation) {
        if(isset($animation)) {
            if(get_class($animation) != Animation::class) {
                throw new WatermarkConfigurationException("Animation should be of type Animation");
            }

            $this->animation = $animation;
        }

        return $this;
    }

    public function move($to_x, $to_y, $startTime, $endTime) {
        $animation = new Animation(["toX" => $toX, "toY" => $toY, "startTime" => $startTime, "endTime" => $endTime]);
        $this->animation = $animation;

        return $this;
    }

    private function isBetweenZeroAndOne($val) {
        if(!$val >= 0 && !$val <= 1) {
            return false;
        }

        return true;
    }
}
