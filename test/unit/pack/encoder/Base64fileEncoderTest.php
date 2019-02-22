<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoItems\test\unit\pack\encoder;

use oat\generis\test\TestCase;
use oat\oatbox\filesystem\File;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoItems\model\pack\encoders\Base64fileEncoder;
use oat\taoMediaManager\model\MediaSource;
use Psr\Http\Message\StreamInterface;

/**
 * @package taoItems
 */
class Base64fileEncoderTest extends TestCase
{

    protected $directoryStorage;


    public function resourceProvider()
    {
        $stream = $this->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $stream->method('getContents')->willReturn('value');

        $mediaSource = $this->getMockBuilder(MediaSource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mediaSource->method('getFileInfo')->willReturn(['mime' => 'text/css']);
        $mediaSource->method('getFileStream')->willReturn($stream);

        $mediaAsset = $this->getMockBuilder(MediaAsset::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mediaAsset->method('getMediaSource')->willReturn($mediaSource);
        $mediaAsset->method('getMediaIdentifier')->willReturn('value');

        $httpSource = $this->getMockBuilder(HttpSource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mediaAssetHttpSource = $this->getMockBuilder(MediaAsset::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mediaAssetHttpSource->method('getMediaSource')->willReturn($httpSource);
        $mediaAssetHttpSource->method('getMediaIdentifier')->willReturn('value');

        return [
            ['exist.css', 'data:text/css;base64,' . base64_encode('value')],
            [$mediaAsset, 'data:text/css;base64,' . base64_encode('value')],
            [$mediaAssetHttpSource, 'value'],
            ['http://google.com/styles.css', 'http://google.com/styles.css']
        ];
    }


    /**
     * Test encoder
     * @dataProvider resourceProvider
     */
    public function testEncode($data, $expected)
    {

        $directoryStorage = $this->getMockBuilder(\tao_models_classes_service_StorageDirectory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('exists')->willReturn(true);
        $file->method('read')->willReturn('value');
        $file->method('getMimeType')->willReturn('text/css');

        $directoryStorage->method('getFile')->with('exist.css')->willReturn($file);

        $encoder = new Base64fileEncoder($directoryStorage, 'en_US');
        $this->assertEquals($expected, $encoder->encode($data));
    }

    /**
     * @expectedException \oat\taoItems\model\pack\ExceptionMissingAsset
     */
    public function testEncodeException()
    {

        $directoryStorage = $this->getMockBuilder(\tao_models_classes_service_StorageDirectory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('exists')->willReturn(false);

        $directoryStorage->method('getFile')->with('notExist.css')->willReturn($file);

        $encoder = new Base64fileEncoder($directoryStorage, 'en_US');
        $this->assertEquals('doesn\'t mater', $encoder->encode('notExist.css'));
    }

}
