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
namespace oat\taoItems\test\pack;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoItems\model\pack\encoders\Base64fileEncoder;

/**
 * @package taoItems
 */
class Base64fileEncoderTest extends TaoPhpUnitTestRunner
{

    protected $directoryStorage;


    public function resourceProvider()
    {
        return [
            ['exist.css', 'data:text/css;base64,' . base64_encode('value')],
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
            ->setConstructorArgs([1, 2, 3, 4])
            ->getMock();

        $directoryStorage->method('has')->with('en_US/exist.css')->willReturn(true);
        $directoryStorage->method('read')->with('en_US/exist.css')->willReturn('value');

        $encoder = new Base64fileEncoder($directoryStorage, 'en_US');
        $this->assertEquals($expected, $encoder->encode($data));
    }

    /**
     * @expectedException \oat\taoItems\model\pack\ExceptionMissingAsset
     */
    public function testEncodeException()
    {

        $directoryStorage = $this->getMockBuilder(\tao_models_classes_service_StorageDirectory::class)
            ->setConstructorArgs([1, 2, 3, 4])
            ->getMock();

        $directoryStorage->method('has')->with('en_US/notExist.css')->willReturn(false);

        $encoder = new Base64fileEncoder($directoryStorage, 'en_US');
        $this->assertEquals('doesn\'t mater', $encoder->encode('notExist.css'));
    }

}
