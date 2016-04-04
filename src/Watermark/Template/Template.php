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

namespace SafeStream\Watermark\Template;

use SafeStream\CustomException;
use SafeStream\Watermark\WatermarkConfiguration;

class TemplateException extends CustomException {}

class Template
{
    public $id;

    public $name;
    
    public $settings = [];

    public function __construct(array $args = [])
    {
        if(isset($args['id'])) {
            $this->id = $args['id'];
        }

        if(isset($args['name'])) {
            $this->name = $args['name'];
        }

        $this->validateWatermarkConfigurations($args['watermarkConfigurations']);

        if(!is_null($args['watermarkConfigurations'])){
            if(is_array($args['watermarkConfigurations'])) {
                foreach ($args['watermarkConfigurations'] as $config) {
                    array_push($this->settings, $config);
                }
            } else {
                array_push($this->settings, $args['watermarkConfigurations']);
            }
        }
    }

    public function withId($id) {
        $this->id = $id;
        return $this;
    }

    public function withName($name) {
        $this->name = $name;
        return $this;
    }

    public function addWatermarkConfiguration(WatermarkConfiguration $watermarkConfiguration) {
        if(is_null($this->settings)) {
            $this->settings = [];
        }
        
        array_push($this->settings, $watermarkConfiguration);

        return $this;
    }

    private function validateWatermarkConfigurations($watermarkConfigurations) {
        $watermarkConfigurationExceptionMessage = "The watermark configuration parameter must
             wither be a WatermarkConfiguration or an array of WatermarkConfiguration";

        if(!is_null($watermarkConfigurations)) {
            if(!is_array($watermarkConfigurations) && get_class($watermarkConfigurations) != WatermarkConfiguration::class) {
                throw new TemplateException($watermarkConfigurationExceptionMessage . "
             instead found " . get_class($watermarkConfigurations));
            } else {
                if(is_array($watermarkConfigurations)) {
                    foreach ($watermarkConfigurations as $config) {
                        if(get_class($config) != WatermarkConfiguration::class) {
                            throw new TemplateException
                            ($watermarkConfigurationExceptionMessage . " instead found " . get_class($config));
                        }
                    }
                }
            }
        }
    }
}