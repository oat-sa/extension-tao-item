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

use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoItems\model\pack\encoders\NoneEncoder;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * @package taoItems
 */
class NoneEncoderTest extends TaoPhpUnitTestRunner
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
     * Test encoder with MediaAsset
     */
    public function testEncodeMediaAsset()
    {
        $encoder = new NoneEncoder();
        $url = 'http://tao.dev/my/asset';
        $asset = new MediaAsset(new HttpSource(), $url);
        $this->assertEquals(basename($url), $encoder->encode($asset));
    }
}
