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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoItems\test\unit\pack;

use InvalidArgumentException;
use oat\generis\test\TestCase;
use oat\taoItems\model\pack\ItemPack;

/**
 * Test the class {@link ItemPack}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class ItemPackTest extends TestCase
{

    /**
     * Test creating an ItemPack
     */
    public function testConstructor()
    {
        $type = 'qti';
        $data = ['foo' => 'bar'];

        $pack = new ItemPack($type, $data);
        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $pack);
        $this->assertEquals($type, $pack->getType());
        $this->assertEquals($data, $pack->getData());
    }

    /**
     * Test assigning assets to a pack
     */
    public function testSetAssets()
    {

        $pack = new ItemPack('qti', ['foo' => 'bar']);
        $jsAssets = [
            'lodash.js' => 'lodash.js',
            'jquery.js' => 'jquery.js'
        ];
        $cssAssets = [
            'style/main.css' => 'style/main.css'
        ];

        $pack->setAssets('js', $jsAssets, null, '');

        $this->assertEquals($jsAssets, $pack->getAssets('js'));
        $this->assertEquals([], $pack->getAssets('css'));


        $pack->setAssets('css', $cssAssets, null, '');

        $this->assertEquals($cssAssets, $pack->getAssets('css'));
    }

    /**
     * Test the constructor with an empty type
     */
    public function testWrongTypeConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ItemPack(null, []);
    }

    /**
     * Test the constructor with invalid data
     */
    public function testWrongDataConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ItemPack('qti', '{"foo":"bar"}');
    }

    /**
     * Test assigning unallowed assets
     */
    public function testWrongAssetType()
    {
        $this->expectException(InvalidArgumentException::class);
        $pack = new ItemPack('qti', ['foo' => 'bar']);
        $pack->setAssets('coffescript', ['jquery.coffee'], null, '');
    }

    /**
     * Test set wrong assets type
     */
    public function testWrongAssets()
    {
        $this->expectException(InvalidArgumentException::class);
        $pack = new ItemPack('qti', ['foo' => 'bar']);
        $pack->setAssets('js', 'jquery.js', null, '');
    }

    /**
     * Provides data to test the bundle
     * @return array() the data
     */
    public function jsonSerializableProvider()
    {

        $data = [];

        $pack1 = new ItemPack('qti', ['foo' => 'bar']);
        $json1 = '{"type":"qti","data":{"foo":"bar"},"assets":[]}';
        $data[0] = [$pack1, $json1];


        $pack2 = new ItemPack('owi', ['foo' => 'bar']);
        $pack2->setAssets('js', [
            'lodash.js',
            'jquery.js'
        ], null, '');
        $json2 = '{"type":"owi","data":{"foo":"bar"},"assets":{"js":{"lodash.js":"lodash.js","jquery.js":"jquery.js"}}}';
        $data[1] = [$pack2, $json2];

        return $data;
    }

    /**
     * Test the itemPack serializaion
     * @param ItemPack $itemPack
     * @param string $expectedJson
     * @dataProvider jsonSerializableProvider
     */
    public function testSerialization($itemPack, $expectedJson)
    {

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertTrue(is_string($expectedJson));
        $this->assertEquals($expectedJson, json_encode($itemPack));
    }
}
