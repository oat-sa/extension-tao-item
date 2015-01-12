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
namespace oat\taoItems\test\pack;

use oat\taoItems\model\pack\ItemPack;
use oat\tao\test\TaoPhpUnitTestRunner;
 

/**
 * Test the class {@link ItemPack}
 *  
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class ItemPackTest extends TaoPhpUnitTestRunner
{

    /**
     * Provides data to test the bundle
     * @return array() the data
     */     
    public function jsonSerializableProvider(){
        $data = array();

        $pack1 = new ItemPack('qti', array('foo' => 'bar'));
        $json1 = '{"type":"qti","data":{"foo":"bar"}}';
        $data[0] = array($pack1, $json1);
    
        return $data;
    }   
 
    /**
     * Test the itemPack serializaion
     * @param ItemPack $itemPack
     * @param string $expectedJson
     * @dataProvider jsonSerializableProvider
     */
    public function testSerialization($itemPack, $expectedJson){
       
       $this->assertInstanceOf('ItemPack', $itemPack);
       $this->assertTrue(is_string($expectedJson));
       $this->assertEquals($expectedJson, json_encode($itemPack));
    }

}
