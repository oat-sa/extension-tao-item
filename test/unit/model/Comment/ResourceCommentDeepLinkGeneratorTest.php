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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\model\Comment;

use InvalidArgumentException;
use oat\tao\model\menu\Perspective;
use oat\tao\model\menu\Section;
use oat\tao\model\menu\Tree;
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\Comment\AssetResourceCommentDeepLinkGenerator;
use oat\taoItems\model\Comment\ItemResourceCommentDeepLinkGenerator;
use oat\taoItems\model\Comment\ResourceCommentDeepLinkGenerator;
use oat\taoItems\model\Comment\ResourceCommentType;
use oat\taoItems\model\Comment\TestResourceCommentDeepLinkGenerator;
use PHPUnit\Framework\TestCase;

class ResourceCommentDeepLinkGeneratorTest extends TestCase
{
    private const CLASS_URI_ASSET = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media';
    private const BASE_URL = 'https://backoffice.ngs.test';

    private ResourceCommentDeepLinkGenerator $sut;

    protected function setUp(): void
    {
        $builder = new CommentMentionDeepLinkBuilder(
            self::BASE_URL,
            [
                $this->perspective('items', 'taoItems', 'manage_items', TaoOntology::CLASS_URI_ITEM),
                $this->perspective('tests', 'taoTests', 'manage_tests', TaoOntology::CLASS_URI_TEST),
                $this->perspective(
                    'taoMediaManager',
                    'taoMediaManager',
                    'media_manager',
                    self::CLASS_URI_ASSET
                ),
            ]
        );

        $this->sut = new ResourceCommentDeepLinkGenerator([
            new ItemResourceCommentDeepLinkGenerator($builder),
            new TestResourceCommentDeepLinkGenerator($builder),
            new AssetResourceCommentDeepLinkGenerator($builder),
        ]);
    }

    public function testEachResourceTypeHasDedicatedGenerator(): void
    {
        $uri = 'https://backoffice.ngs.test/ontologies/tao.rdf#i1';

        $itemUrl = $this->sut->build(ResourceCommentType::ITEM, $uri);
        $testUrl = $this->sut->build(ResourceCommentType::TEST, $uri);
        $assetUrl = $this->sut->build(ResourceCommentType::ASSET, $uri);

        $this->assertStringContainsString('structure=items', $itemUrl);
        $this->assertStringContainsString('ext=taoItems', $itemUrl);
        $this->assertStringContainsString('section=manage_items', $itemUrl);

        $this->assertStringContainsString('structure=tests', $testUrl);
        $this->assertStringContainsString('ext=taoTests', $testUrl);
        $this->assertStringContainsString('section=manage_tests', $testUrl);

        $this->assertStringContainsString('structure=taoMediaManager', $assetUrl);
        $this->assertStringContainsString('ext=taoMediaManager', $assetUrl);
        $this->assertStringContainsString('section=media_manager', $assetUrl);
    }

    public function testRejectsUnregisteredResourceType(): void
    {
        $sut = new ResourceCommentDeepLinkGenerator([
            new ItemResourceCommentDeepLinkGenerator(
                new CommentMentionDeepLinkBuilder(self::BASE_URL, [])
            ),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No comment deep-link generator registered');

        $sut->build(ResourceCommentType::TEST, 'https://example/rdf#i1');
    }

    private function perspective(
        string $id,
        string $ext,
        string $sectionId,
        string $rootNode
    ): Perspective {
        $tree = new Tree(['rootNode' => $rootNode, 'name' => 'tree']);
        $section = new Section(
            [
                'id' => $sectionId,
                'name' => $sectionId,
                'url' => '/',
                'extension' => $ext,
                'controller' => 'X',
                'action' => 'index',
                'binding' => null,
                'policy' => Section::POLICY_MERGE,
                'disabled' => false,
            ],
            [$tree],
            []
        );

        return new Perspective(
            [
                'id' => $id,
                'extension' => $ext,
                'name' => $id,
                'group' => Perspective::GROUP_DEFAULT,
                'level' => '0',
                'description' => '',
                'binding' => null,
                'icon' => null,
            ],
            [$section]
        );
    }
}
