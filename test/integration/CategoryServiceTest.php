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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 */

namespace oat\taoItems\test\integration;

use oat\generis\model\GenerisRdf;
use Prophecy\Argument;
use Prophecy\Prophet;
use core_kernel_classes_Class    as RdfClass;
use core_kernel_classes_Property as RdfProperty;
use core_kernel_classes_Resource as RdfResource;
use oat\taoItems\model\CategoryService;
use oat\tao\test\TaoPhpUnitTestRunner;
use taoItems_models_classes_ItemsService;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * CategoryService test
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class CategoryServiceTest extends TaoPhpUnitTestRunner
{
    /**
     * Tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * testSanitize data provider
     */
    public function categoryNames()
    {
        return [
            [" < Hello   My w!%'orld!! ", "hello-my-world"],
            ["12hello", "hello"],
            [" hello", "hello"],
            ["!1h12ello ", "h12ello"],
            ["<span class='hello'>&nbsp;''hello</span> ", "span-classhellonbsphellospan"],
            ["averylongnamethatexceedtheexpectedthritytowcharacters", "averylongnamethatexceedtheexpect"]
        ];
    }

    /**
     * Test CategoryService#doesExpose
     *
     * @dataProvider categoryNames
     */
    public function testSanitize($value, $expected)
    {
        $this->assertEquals($expected, CategoryService::sanitizeCategoryName($value), 'The values are sanitized');
    }

    /**
     * Test CategoryService#doesExpose
     */
    public function testDoesExpose()
    {
        $categoryService = new CategoryService();
        $exposeProperty  = new RdfProperty(CategoryService::EXPOSE_PROP_URI);
        $trueResource    = new RdfResource(GenerisRdf::GENERIS_TRUE);
        $falseResource   = new RdfResource(GenerisRdf::GENERIS_FALSE);

        //prop value is GENERIS TRUE
        $propProphecy = $this->prophesize('\core_kernel_classes_Property');
        $propProphecy->getOnePropertyValue($exposeProperty)->willReturn($trueResource);
        $this->assertTrue($categoryService->doesExposeCategory($propProphecy->reveal()), 'The property is exposed');

        //prop value is GenerisRdf::GENERIS_FALSE
        $propProphecy = $this->prophesize('\core_kernel_classes_Property');
        $propProphecy->getOnePropertyValue($exposeProperty)->willReturn($falseResource);
        $this->assertFalse($categoryService->doesExposeCategory($propProphecy->reveal()), 'The property is not exposed');

        //no prop value
        $propProphecy = $this->prophesize('\core_kernel_classes_Property');
        $propProphecy->getOnePropertyValue($exposeProperty)->willReturn(null);

        $this->assertFalse($categoryService->doesExposeCategory($propProphecy->reveal()), 'The property is not exposed');
    }

    /**
     * Test CategoryService#getElligibleProperties
     */
    public function testGetElligibleProperties()
    {
        $fooClass = new RdfClass('foo');

        $eligibleProp1 = $this->prophesize('\core_kernel_classes_Property');
        $eligibleProp1->getWidget()->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[0]));
        $eligibleProp1->getUri()->willReturn('p1');

        $eligibleProp2 = $this->prophesize('\core_kernel_classes_Property');
        $eligibleProp2->getWidget()->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[2]));
        $eligibleProp2->getUri()->willReturn('p2');

        $notEligibleProp1 = $this->prophesize('\core_kernel_classes_Property');
        $notEligibleProp1->getWidget()->willReturn(null);
        $notEligibleProp1->getUri()->willReturn('np1');

        $notEligibleProp2 = $this->prophesize('\core_kernel_classes_Property');
        $notEligibleProp2->getWidget()->willReturn(new RdfResource('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HtmlBox'));
        $notEligibleProp2->getUri()->willReturn('np2');

        $excludedProp1 = $this->prophesize('\core_kernel_classes_Property');
        $excludedProp1->getWidget()->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[0]));
        $excludedProp1->getUri()->willReturn(CategoryService::$excludedPropUris[0]);

        $itemService = $this->prophesize('\taoItems_models_classes_ItemsService');
        $itemService->getClazzProperties($fooClass, Argument::any())->willReturn([
            $eligibleProp1->reveal(),
            $eligibleProp2->reveal(),
            $notEligibleProp1->reveal(),
            $notEligibleProp2->reveal(),
            $excludedProp1->reveal()
        ]);

        $categoryService = new CategoryService();
        $categoryService->setItemService($itemService->reveal());

        $result = $categoryService->getElligibleProperties($fooClass);

        $this->assertEquals(2, count($result), "We have 2 eligible properties");
        $this->assertEquals('p1', $result[0]->getUri(), "We have an eligible properties");
        $this->assertEquals('p2', $result[1]->getUri(), "We have an eligible properties");
    }

    /**
     * Test CategoryService#getItemCategories
     */
    public function testGetItemCategories()
    {
        $fooClass = new RdfClass('foo');

        $eligibleProp1 = $this->prophesize('\core_kernel_classes_Property');
        $eligibleProp1->getWidget()->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[0]));
        $eligibleProp1->getUri()->willReturn('p1');

        $eligibleProp2 = $this->prophesize('\core_kernel_classes_Property');
        $eligibleProp2->getWidget()->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[2]));
        $eligibleProp2->getUri()->willReturn('p2');

        $p2Value = $this->prophesize('\core_kernel_classes_Resource');
        $p2Value->getLabel()->willReturn('Yeah Moo');

        $item = $this->prophesize('\core_kernel_classes_Resource');
        $item->getPropertiesValues(Argument::any())->willReturn([
            'p1' => ['Foo', 'Yo _Bar '],
            'p2' => [$p2Value->reveal()]
        ]);
        $item->getTypes()->willReturn([$fooClass]);

        $itemService = $this->prophesize('\taoItems_models_classes_ItemsService');
        $itemService->getClazzProperties($fooClass, Argument::any())->willReturn([
            'p1' => $eligibleProp1->reveal(),
            'p2' => $eligibleProp2->reveal()
        ]);

        $categoryService = new CategoryService();
        $categoryService->setItemService($itemService->reveal());

        $categories = $categoryService->getItemCategories($item->reveal());

        $this->assertEquals(3, count($categories), "We have 3 categories");
        $this->assertEquals('foo', $categories[0], "The category matches");
        $this->assertEquals('yo-bar', $categories[1], "The category matches");
        $this->assertEquals('yeah-moo', $categories[2], "The category matches");
    }
}
