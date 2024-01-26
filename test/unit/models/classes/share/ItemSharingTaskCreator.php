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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\share;

use oat\generis\model\data\Ontology;
use oat\generis\test\OntologyMockTrait;
use oat\tao\helpers\FileHelperService;
use oat\tao\model\taskQueue\QueueDispatcher;
use oat\taoItems\model\share\ItemSharingTaskCreator;
use PHPUnit\Framework\TestCase;
use core_kernel_classes_Resource as Resource;

class ItemSharingTaskCreatorTest extends TestCase
{
    use OntologyMockTrait;
    private $queueDispatcherMock;
    private $ontologyMock;
    private $fileHelperServiceMock;
    private $subject;
    protected function setUp(): void
    {
        $this->queueDispatcherMock = $this->createMock(QueueDispatcher::class);
        $this->ontologyMock = $this->createMock(Ontology::class);
        $this->fileHelperServiceMock = $this->createMock(FileHelperService::class);
        $this->ontologyMock = $this->getOntologyMock();

        $this->subject = new ItemSharingTaskCreator(
            $this->queueDispatcherMock,
            $this->ontologyMock,
            $this->fileHelperServiceMock
        );
    }

    public function testCreateTask(): void
    {
        $resourceMock = $this->createMock(Resource::class);

        $this->ontologyMock
            ->expects($this->once())
            ->method('getResource')
            ->with('SomeUniqueResourceUri')
            ->willReturn($resourceMock);

        $resourceMock->expects($this->once())
            ->method('getLabel')
            ->willReturn('SomeLabel');

        $this->queueDispatcherMock
            ->expects($this->once())
            ->method('createTask')
            ->with(
                $this->anything(),
                [
                    'exportData' => [
                        'label' => 'SomeLabel',
                        'instances' => $this->anything(),
                        'destination' => $this->anything(),
                        'filename' => $this->anything(),
                        'resourceUri' => 'SomeUniqueResourceUri',
                    ],
                ],
                'Sharing resource SomeLabel'
            );


        $parsedBody = [
            'id' => 'SomeUniqueResourceUri',
        ];

        $this->subject->createTask($parsedBody);
    }
}
