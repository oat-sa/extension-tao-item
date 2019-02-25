<?php
/**
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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoItems\test\unit\preview;

use oat\generis\test\TestCase;
use oat\oatbox\service\ConfigurableService;
use oat\tao\model\ClientLibConfigRegistry;
use oat\tao\model\modules\DynamicModule;
use oat\taoItems\model\preview\ItemPreviewerService;
use Prophecy\Argument;
use Prophecy\Prophet;

/**
 * Test the ItemPreviewerService
 *
 * @author Jean-Sébastien Conan <jean-sebastien@taotesting.com>
 */
class ItemPreviewerServiceTest extends TestCase
{

    //data to stub the registry content
    private static $adapterData = [
        'taoItems/previewer/factory' => [
            'previewers' => [
                'taoQtiTest/previewer/adapter/qtiItem' => [
                    'id' => 'qtiItem',
                    'module' => 'taoQtiTest/previewer/adapter/qtiItem',
                    'bundle' => 'taoQtiTest/loader/qtiPreviewer.min',
                    'position' => null,
                    'name' => 'QTI Item Previewer',
                    'description' => 'QTI implementation of the item previewer',
                    'category' => 'previewer',
                    'active' => true,
                    'tags' => [
                        'core',
                        'qti',
                        'previewer'
                    ]
                ],

                'taoQtiTest/previewer/adapter/qtiTest' => [
                    'id' => 'qtiTest',
                    'module' => 'taoQtiTest/previewer/adapter/qtiTest',
                    'bundle' => 'taoQtiTest/loader/qtiPreviewer.min',
                    'position' => null,
                    'name' => 'QTI Test Previewer',
                    'description' => 'QTI implementation of the test previewer',
                    'category' => 'previewer',
                    'active' => false,
                    'tags' => [
                        'core',
                        'qti',
                        'previewer'
                    ]
                ]
            ]
        ]
    ];

    /**
     * Get the service with the stubbed registry
     * @return ItemPreviewerService
     */
    protected function getItemPreviewerService()
    {
        $itemPreviewerService = new ItemPreviewerService();

        $prophet = new Prophet();
        $prophecy = $prophet->prophesize(ClientLibConfigRegistry::class);

        $data = self::$adapterData;
        $prophecy->isRegistered(Argument::type('string'))->will(function ($args) use (&$data) {
            return isset($data[$args[0]]);
        });
        $prophecy->get(Argument::type('string'))->will(function ($args) use (&$data) {
            return $data[$args[0]];
        });
        $prophecy->set(Argument::type('string'), Argument::type('array'))->will(function ($args) use (&$data) {
            $data[$args[0]] = $args[1];
        });
        $itemPreviewerService->setRegistry($prophecy->reveal());

        return $itemPreviewerService;
    }

    /**
     * Check the service is a service
     */
    public function testApi()
    {
        $itemPreviewerService = $this->getItemPreviewerService();
        $this->assertInstanceOf(ItemPreviewerService::class, $itemPreviewerService);
        $this->assertInstanceOf(ConfigurableService::class, $itemPreviewerService);
    }

    /**
     * Test the method ItemPreviewerService::getAdapters
     */
    public function testGetAdapters()
    {
        $itemPreviewerService = $this->getItemPreviewerService();

        $adapters = $itemPreviewerService->getAdapters();

        $this->assertEquals(2, count($adapters));

        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiItem', $adapters);
        $adapter0 = $adapters['taoQtiTest/previewer/adapter/qtiItem'];
        $this->assertArrayHasKey('id', $adapter0);
        $this->assertArrayHasKey('module', $adapter0);
        $this->assertArrayHasKey('bundle', $adapter0);
        $this->assertArrayHasKey('category', $adapter0);
        $this->assertArrayHasKey('active', $adapter0);

        $this->assertEquals('qtiItem', $adapter0['id']);
        $this->assertEquals('taoQtiTest/previewer/adapter/qtiItem', $adapter0['module']);
        $this->assertEquals('taoQtiTest/loader/qtiPreviewer.min', $adapter0['bundle']);
        $this->assertEquals('previewer', $adapter0['category']);
        $this->assertEquals(true, $adapter0['active']);

        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiTest', $adapters);
        $adapter1 = $adapters['taoQtiTest/previewer/adapter/qtiTest'];
        $this->assertArrayHasKey('id', $adapter1);
        $this->assertArrayHasKey('module', $adapter1);
        $this->assertArrayHasKey('bundle', $adapter1);
        $this->assertArrayHasKey('category', $adapter1);
        $this->assertArrayHasKey('active', $adapter1);

        $this->assertEquals('qtiTest', $adapter1['id']);
        $this->assertEquals('taoQtiTest/previewer/adapter/qtiTest', $adapter1['module']);
        $this->assertEquals('taoQtiTest/loader/qtiPreviewer.min', $adapter1['bundle']);
        $this->assertEquals('previewer', $adapter1['category']);
        $this->assertEquals(false, $adapter1['active']);
    }

    /**
     * Test the method ItemPreviewerService::registerAdapter
     */
    public function testRegisterAdapter()
    {
        $itemPreviewerService = $this->getItemPreviewerService();

        $adapters = $itemPreviewerService->getAdapters();

        $this->assertEquals(2, count($adapters));

        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiItem', $adapters);
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiTest', $adapters);
        $this->assertArrayNotHasKey('taoQtiTest/previewer/adapter/qtiMock', $adapters);


        $module = DynamicModule::fromArray([
            'id' => 'qtiMock',
            'name' => 'QTI Mock Previewer',
            'module' => 'taoQtiTest/previewer/adapter/qtiMock',
            'bundle' => 'taoQtiTest/loader/qtiPreviewer.min',
            'description' => 'QTI implementation of the item previewer',
            'category' => 'previewer',
            'active' => true,
            'tags' => ['core', 'qti', 'previewer']
        ]);
        $this->assertEquals(true, $itemPreviewerService->registerAdapter($module));

        $adapters = $itemPreviewerService->getAdapters();
        $this->assertEquals(3, count($adapters));
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiItem', $adapters);
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiTest', $adapters);
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiMock', $adapters);
    }

    /**
     * Test the method ItemPreviewerService::unregisterAdapter
     */
    public function testUnregisterAdapter()
    {
        $itemPreviewerService = $this->getItemPreviewerService();

        $adapters = $itemPreviewerService->getAdapters();

        $this->assertEquals(2, count($adapters));

        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiItem', $adapters);
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiTest', $adapters);
        
        $this->assertEquals(true, $itemPreviewerService->unregisterAdapter('taoQtiTest/previewer/adapter/qtiTest'));

        $adapters = $itemPreviewerService->getAdapters();
        $this->assertEquals(1, count($adapters));
        $this->assertArrayHasKey('taoQtiTest/previewer/adapter/qtiItem', $adapters);
        $this->assertArrayNotHasKey('taoQtiTest/previewer/adapter/qtiTest', $adapters);

        $this->assertEquals(false, $itemPreviewerService->unregisterAdapter('taoQtiTest/previewer/adapter/qtiTest'));
    }

}
