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

namespace oat\taoItems\model\share;

use core_kernel_classes_Resource as Resource;
use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use oat\generis\model\data\Ontology;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\filesystem\FileSystemService;
use oat\tao\helpers\FileHelperService;
use oat\tao\model\taskQueue\QueueDispatcher;
use oat\tao\model\taskQueue\Task\AbstractTask;
use oat\tao\model\taskQueue\Task\TaskInterface;
use oat\taoItems\model\share\task\ItemSharingTask;
use oat\taoQtiItem\model\Export\QtiPackage22ExportHandler;
use oat\taoQtiItem\model\Export\QTIPackedItem22Exporter;
use tao_helpers_File as FileHelper;

class ItemSharingService
{
    use OntologyAwareTrait;

    private $queueDispatcher;
    private $ontology;
    private $fileHelperService;
    public function __construct(
        QueueDispatcher $queueDispatcher,
        Ontology $ontology,
        FileHelperService $fileHelperService,
    )
    {
        $this->queueDispatcher = $queueDispatcher;
        $this->ontology = $ontology;
        $this->fileHelperService = $fileHelperService;
    }

    public function createTask(array $parsedBody): void
    {
        $label = $this->getResource($parsedBody['id'])->getLabel();
        $this->queueDispatcher->createTask(
            new ItemSharingTask(),
            [
                ItemSharingTask::PARAM_EXPORT_DATA => [
                    ItemSharingTask::LABEL => $label,
                    ItemSharingTask::INSTANCES => $this->getContentToShare($parsedBody),
                    ItemSharingTask::DESTINATION => $this->fileHelperService->createTempDir(),
                    ItemSharingTask::FILENAME => FileHelper::getSafeFileName($parsedBody['id']),
                    ItemSharingTask::RESOURCE_URI => $parsedBody['id']
                ],
            ],
            sprintf('Sharing resource %s', $label)
        );
    }

    private function getContentToShare(array $reqParsedBody): array
    {
        if (!isset($reqParsedBody['id'])) {
            throw new InvalidArgumentException;
        }

        return $this->getNestedItemsRecursive(
            $this->ontology->getResource($reqParsedBody['id'])
        );
    }

    private function getNestedItemsRecursive(Resource $resource): array
    {
        if ($resource->isClass() === false) {
            return [
                $resource->getUri()
            ];
        }

        return array_column(
            array_filter($resource->getNestedResources(), function ($item) {
                return $item['isclass'] === 0;
            }),
            'id'
        );
    }
}
