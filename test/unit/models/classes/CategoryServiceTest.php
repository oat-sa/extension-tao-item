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
 * Copyright (c) 2016-2020 (original work) Open Assessment Technologies SA
 */

namespace oat\taoItems\test\unit\models\classes;

use core_kernel_classes_Class as RdfClass;
use core_kernel_classes_Property as RdfProperty;
use core_kernel_classes_Resource as RdfResource;
use oat\generis\model\GenerisRdf;
use PHPUnit\Framework\TestCase;
use oat\taoItems\model\CategoryService;
use Prophecy\Argument;
use taoItems_models_classes_ItemsService;

/**
 * CategoryService test
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class CategoryServiceTest extends TestCase
{
    /**
     * testSanitize data provider
     */
    public function categoryNames()
    {
        return [
            [" < Hello   My w!%'orld!! ", 'hello-my-world'],
            ['12hello', 'hello'],
            [' hello', 'hello'],
            ['!1h12ello ', 'h12ello'],
            ["<span class='hello'>&nbsp;''hello</span> ", 'span-classhellonbsphellospan'],
            ['averylongnamethatexceedtheexpectedthritytowcharacters', 'averylongnamethatexceedtheexpect'],
            ['ÆØÅ', 'æøå'],
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
        $prop1 = $this->createMock(RdfProperty::class);
        $prop1->method('getOnePropertyValue')->with($exposeProperty)->willReturn($trueResource);
        $this->assertTrue($categoryService->doesExposeCategory($prop1), 'The property is exposed');

        //prop value is GenerisRdf::GENERIS_FALSE
        $prop2 = $this->createMock(RdfProperty::class);
        $prop2->method('getOnePropertyValue')->with($exposeProperty)->willReturn($falseResource);
        $this->assertFalse(
            $categoryService->doesExposeCategory($prop2),
            'The property is not exposed'
        );

        //no prop value
        $prop3 = $this->createMock(RdfProperty::class);
        $prop3->method('getOnePropertyValue')->with($exposeProperty)->willReturn(null);

        $this->assertFalse(
            $categoryService->doesExposeCategory($prop3),
            'The property is not exposed'
        );
    }

    /**
     * Test CategoryService#getElligibleProperties
     */
    public function testGetElligibleProperties()
    {
        $fooClass = new RdfClass('foo');

        $eligibleProp1 = $this->createMock(RdfProperty::class);
        $eligibleProp1
            ->method('getWidget')
            ->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[0]));
        $eligibleProp1->method('getUri')->willReturn('p1');

        $eligibleProp2 = $this->createMock(RdfProperty::class);
        $eligibleProp2
            ->method('getWidget')
            ->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[2]));
        $eligibleProp2->method('getUri')->willReturn('p2');

        $notEligibleProp1 = $this->createMock(RdfProperty::class);
        $notEligibleProp1->method('getWidget')->willReturn(null);
        $notEligibleProp1->method('getUri')->willReturn('np1');

        $notEligibleProp2 = $this->createMock(RdfProperty::class);
        $notEligibleProp2
            ->method('getWidget')
            ->willReturn(new RdfResource('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HtmlBox'));
        $notEligibleProp2->method('getUri')->willReturn('np2');

        $excludedProp1 = $this->createMock(RdfProperty::class);
        $excludedProp1->method('getUri')->willReturn(CategoryService::$excludedPropUris[0]);

        $itemService = $this->createMock(taoItems_models_classes_ItemsService::class);
        $itemService
            ->method('getClazzProperties')
            ->with($fooClass, $this->anything())
            ->willReturn([
                $eligibleProp1,
                $eligibleProp2,
                $notEligibleProp1,
                $notEligibleProp2,
                $excludedProp1
            ]);

        $categoryService = new CategoryService();
        $categoryService->setItemService($itemService);

        $result = $categoryService->getElligibleProperties($fooClass);

        $this->assertCount(2, $result, "We have 2 eligible properties");
        $this->assertEquals('p1', $result[0]->getUri(), "We have an eligible properties");
        $this->assertEquals('p2', $result[1]->getUri(), "We have an eligible properties");
    }

    /**
     * Test CategoryService#getItemCategories
     */
    public function testGetItemCategories()
    {
        $fooClass       = new RdfClass('foo');
        $exposeProperty = new RdfProperty(CategoryService::EXPOSE_PROP_URI);
        $trueResource   = new RdfResource(GenerisRdf::GENERIS_TRUE);
        $falseResource  = new RdfResource(GenerisRdf::GENERIS_FALSE);

        $eligibleProp1 = $this->createMock(RdfProperty::class);
        $eligibleProp1->method('getOnePropertyValue')->with($exposeProperty)->willReturn($trueResource);
        $eligibleProp1->method('getWidget')->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[0]));
        $eligibleProp1->method('getUri')->willReturn('p1');

        $eligibleProp2 = $this->createMock(RdfProperty::class);
        $eligibleProp2->method('getOnePropertyValue')->with($exposeProperty)->willReturn($trueResource);
        $eligibleProp2->method('getWidget')->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[2]));
        $eligibleProp2->method('getUri')->willReturn('p2');

        $eligibleProp3 = $this->createMock(RdfProperty::class);
        $eligibleProp3->method('getOnePropertyValue')->with($exposeProperty)->willReturn($trueResource);
        $eligibleProp3->method('getWidget')->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[3]));
        $eligibleProp3->method('getUri')->willReturn('p3');

        $notEligibleProp1 = $this->createMock(RdfProperty::class);
        $notEligibleProp1->method('getOnePropertyValue')->with($exposeProperty)->willReturn($falseResource);
        $notEligibleProp1->method('getWidget')->willReturn(new RdfResource(CategoryService::$supportedWidgetUris[2]));
        $notEligibleProp1->method('getUri')->willReturn('np1');

        $p2Value = $this->createMock(RdfResource::class);
        $p2Value->method('getLabel')->willReturn('Yeah Moo');

        $item = $this->createMock(RdfResource::class);
        $item
            ->method('getPropertiesValues')
            ->with(['p1', 'p2', 'p3'])
            ->willReturn(
                [
                    'p1' => ['Foo', 'Yo _Bar '],
                    'p2' => [$p2Value],
                    'p3' => [''],
                ]
            );
        $item->method('getTypes')->willReturn([$fooClass]);

        $itemService = $this->createMock('\taoItems_models_classes_ItemsService');
        $itemService
            ->method('getClazzProperties')
            ->with($fooClass, $this->anything())
            ->willReturn(
                [
                    'p1'  => $eligibleProp1,
                    'p2'  => $eligibleProp2,
                    'p3'  => $eligibleProp3,
                    'np1' => $notEligibleProp1,
                ]
            );

        $categoryService = new CategoryService();
        $categoryService->setItemService($itemService);

        $categories = $categoryService->getItemCategories($item);

        $this->assertCount(3, $categories, "We have 3 categories");
        $this->assertEquals('foo', $categories[0], "The category matches");
        $this->assertEquals('yo-bar', $categories[1], "The category matches");
        $this->assertEquals('yeah-moo', $categories[2], "The category matches");
    }
}
