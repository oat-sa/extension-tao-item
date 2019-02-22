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
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoItems\model\media\LocalItemSource;
use oat\taoItems\model\pack\encoders\NoneEncoder;

/**
 * @package taoItems
 */
class NoneEncoderTest extends TestCase
{

    /**
     * Test encoder
     */
    public function testEncode()
    {
        $encoder = new NoneEncoder();
        $this->assertEquals('value', $encoder->encode('value'));
    }

    /**
     * Test encoder with Http MediaAsset
     */
    public function testEncodeHttpMediaAsset()
    {
        $encoder = new NoneEncoder();
        $url = 'https://www.taotesting.com/wp-content/uploads/2014/09/oat-header-logo.png';
        $asset = new MediaAsset(new HttpSource(), $url);
        $this->assertEquals($url, $encoder->encode($asset));
    }

    /**
     * Test encoder with MediaAsset
     */
    public function testEncodeMediaAsset()
    {
        $encoder = new NoneEncoder();
        $url = 'assets/test.png';
        $asset = new MediaAsset(new LocalItemSource(array()), $url);
        $this->assertEquals(basename($url), $encoder->encode($asset));
    }
}
