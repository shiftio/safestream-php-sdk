![SafeStream logo](https://www.filepicker.io/api/file/4kub5cbVRLmfteT3vqFK)

## Installing the SafeStream SDK

The recommended way to install the SDK is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Guzzle:

```bash
composer.phar require safestream/safestream-php-sdk
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update the SDK using composer:

 ```bash
composer.phar update
 ```
 
## Getting Started
#### SafeStreamClient
First, you'll need to instantiate a new SafeStreamClient. Through the client, you can access all of SafeStream's functionality. Creating the client is simple. You only need your API key.
```php
$safeStreamClient = new \SafeStream\SafeStreamClient(["apiKey" => "YOUR API KEY"]);
 ```
 
#### Videos
Before SafeStream can watermark your videos you first need to tell SafeStream about them. To do this you just need to give SafeStream the URL to your video and it will download it and prepare it for watermarking. This step typically takes half or real time. Meaning that a 5 minute video would take 2-3 minutes.

#####Functions
######create(Video $video, $waitForIngest = 0)

| Argument  | Type | Description |
| ------------- | ------------- | ------------- |
| video  | Video  | The video to be ingested  |
| waitForIngest  | long  | The time (in millis) to wait for ingest to complete before returning. Video ingest typically takes approxamately half of realtime. Meaning, a 5 minute video would finish in 2-3 minutes  |

######createFromSourceUrl($sourceUrl, $waitForIngest = 0)
| Argument  | Type | Description |
| ------------- | ------------- | ------------- |
| sourceUrl  | string  | The URL of the video to be ingested  |
| waitForIngest  | long  | The time (in millis) to wait for ingest to complete before returning. Video ingest typically takes approxamately half of realtime. Meaning, a 5 minute video would finish in 2-3 minutes  |

######find($svideoKey)
| Argument  | Type | Description |
| ------------- | ------------- | ------------- |
| videoKey  | string  | The video key that you gave when creating the video. If no key was given then the key value is the same as the videos sourceUrl  |


##### Examples
###### Here's a simple example of creating a video:

```php
$safeStreamClient.video().createFromSourceUrl("https://example.com/my-video.mp4");
```

You can also give your video's custom keys to make it easier to find them later. For example, if you have a video in your own system that you've named "red-carpet-reel-20" you can give SafeStream this key and will store it with the video. This way, you don't have to store SafeStream id's if you don't want to. 

###### Here's a simple example of creating a video with a custom key:
```php
$safeStreamClient.video().create(["sourceUrl" => "https://example.com/my-video.mp4", "key" => "red-carpet-reel-20"]);
```
#### Watermarking Videos
##### Function
###### create($videoKey, $watermarkConfiguration, $timeout = 90000)
| Argument  | Type | Description |
| ------------- | ------------- | ------------- |
| videoKey  | string  | The video key that you gave when creating the video. If no key was given then the key value is the same as the videos sourceUrl  |
| watermarkConfiguration  | WatermarkConfiguration OR Array of WatermarkConfiguration  | See [Watermark Configuration Properties](#watermark-configuration-properties) |
| timeout  | long  | Time (in millis) to wait for watermarking to complete. Most watermarking will be complete in < 30 seconds  |

###### createFromTemplate($videoKey, $templateId, $templateMapping, $timeout = 90000)
| Argument  | Type | Description |
| ------------- | ------------- | ------------- |
| videoKey  | string  | The video key that you gave when creating the video. If no key was given then the key value is the same as the videos sourceUrl  |
| templateId  | string  | See [Templates](#templates) |
| templateMapping  | Map (Key / Values)  | See [Templates](#templates) |
| timeout  | long  | Time (in millis) to wait for watermarking to complete. Most watermarking will be complete in < 30 seconds  |
##### Examples
###### Basic Watermark
```php
$watermarkConfiguration = new \SafeStream\Watermark\WatermarkConfiguration(["content" => "YOUR NAME"]);
$safeStreamClient->watermark()->create("YOUR VIDEO KEY", $watermarkConfiguration, 90000);
```
#### Watermarking Videos from a Template
Instead of passing in the watermark configuration each time you watermark a video you can use templates. Templates are stored watermark configuration with varible text in the content field. See blah for more on creating templates.
```php
$watermarkConfiguration = new \SafeStream\Watermark\WatermarkConfiguration(["content" => "YOUR NAME"]);
$safeStreamClient->watermark()->createFromTemplate("YOUR VIDEO KEY", "TEMPLATE ID", array("first_name", "Joe"));
```

The above example assumes you have a template with a variable place holder, "first_name" which might look something like:
```json
 {
  "settings": [
    {
      "content": "First Name [%first_name%]",
      "horizontalAlignment": "CENTER",
      "verticalAlignment": "MIDDLE",
      "fontColor": "0xffffff",
      "shadowOpacity": 0.1,
      "fontOpacity": 0.3,
      "type": "TEXT",
      "shadowOffsetX": 0.08,
      "fontSize": 0.05,
      "shadowColor": "0x000000",
      "y": 0.5,
      "x": 0.5,
      "shadowOffsetY": 0.08
    }
  ]
}
```

#### Watermark Configuration Properties
Name | Description
------------ | -------------
content | The text string to watermark onto the video.
type | Currently <code>TEXT</code> is support
horizontalAlignment | Specifies the horizontal anchor point on the watermark. A value of LEFT will anchor the watermark on it's right most point. A value of RIGHT will anchor the watermark on it's left most pixel. A value of CENTER will anchor the watermark in it's center most pixel.
verticalAlignment | Specifies the vertical anchor point on the watermark. A value of TOP will anchor the watermark on it's top most point. A value of MIDDLE will anchor the watermark on it's middle most pixel. A value of BOTTOM will anchor the watermark in it's bottom most pixel.
x | The relative x position of the anchor. The position is relative to the width of the video. So, a video with a width of 1080 and an x value of .5 will put the anchor point at 540 pixels. The anchor position is defined by the horizontal and vertical alignment.
y | The relative y position of the anchor. The position is relative to the height of the video. So, a video with a height of 720 and an y value of .5 will put the anchor point at 360 pixels. The anchor position is defined by the horizontal and vertical alignment.
fontSize | Size of the watermark text relative to the height of the video. For example, a video with a height of 720 and a font size of .05 will produce a watermark with a text font size of 36.
fontOpacity |Values from 0 (totally transparent) to 1 (totally opaque)
fontColor | Hex value of font color (ex 0xffffff)
shadowOpacity | Opacity of the drop shadow of the watermark text. 0 (totally transparent) to 1 (totally opaque)
shadowColor | Hex value of watermark text drop shadow color (ex 0xffffff)
shadowOffsetX | Horizontal offset of the drop shadow
shadowOffsetY | Vertical offset of the drop shadow

#### Watermarking Templates
Watermarking templates allow you to store pre-configured watermark settings in SafeStream. This allows you to avoid having to send watermark configurations with each watermarking request. The Templates allow you to set all of the watermark configuration properties but also allow you to set the content field to contain variable content which would be hydrated during subsequent watermark requests.

Creating a new template is simple:
```php
$watermarkConfiguration = new \SafeStream\Watermark\WatermarkConfiguration(["content" => "[%first_name%]"]);
$template = new \SafeStream\Watermark\Template\Template();
$template->addWatermarkConfiguration($watermarkConfiguration);
$safeStreamClient->watermark()->template()->save($template);
```

And then to use if for watermarking:
```php
$watermarkClient->watermark()->createFromTemplate("YOUR VIDEO KEY", "YOUR TEMPLATE ID", array("first_name" => "Joe"));
```
