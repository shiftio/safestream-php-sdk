<?php
declare(strict_types=1);

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

/**
 * @covers SafeStreamClient
 */
final class SafeStreamClientTests extends TestCase
{
    public function testThatWatermarkRequestStarts()
    {
        $name = "Sample User";
        $email = "test@safestream.com";
        $company = "Acme Studios 22";

        // Instantiate the SafeStreamClient using your own API Key
        $safeStreamClient = new \SafeStream\SafeStreamClient(["protocol" => "https", "hostName" => "api.safestream.com", "clientId" => $_SERVER["SAFESTREAM_CLIENT_ID"], "apiKey" => $_SERVER["SAFESTREAM_API_KEY"]]);

        // Configuration for the Name
        $watermarkConfiguration1 = new \SafeStream\Watermark\WatermarkConfiguration([
            "content" => "Licensed to " . $name . time(),
            "fontColor" => "FFFFFF",
            "y" => 0.83,
            "x" => 0.03,
            "fontOpacity" => 0.5,
            "fontSize" => 0.03,
            "horizontalAlignment" => "LEFT",
            "verticalAlignment"  => "TOP"
        ]);

        // Configuration for the Company
        $watermarkConfiguration2 = new \SafeStream\Watermark\WatermarkConfiguration([
            "content" => $company,
            "fontColor" => "FFFFFF",
            "y" =>   0.04,
            "x" =>  0.97,
            "fontOpacity" => 0.5,
            "fontSize" => 0.03,
            "horizontalAlignment" => "RIGHT",
            "verticalAlignment"  => "TOP",
            "shadowColor"  => "000000",
            "shadowOffsetX"  => 0.08,
            "shadowOffsetY" => 0.08,
            "shadowOpacity" => 0.33
        ]);

        $mydata = $safeStreamClient -> watermark()->create("feature-1",array($watermarkConfiguration1,$watermarkConfiguration2),0);

        return $this->assertEquals($mydata->status, "REQUESTED");
    }
}