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

namespace oat\taoItems\model\share\task;

use Exception;
use League\Flysystem\FilesystemInterface;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\filesystem\FileSystemService;
use oat\tao\model\taskQueue\TaskLogActionTrait;
use oat\taoQtiItem\model\Export\QtiPackage22ExportHandler;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ItemSharingTask extends AbstractAction
{
    use ContainerAwareTrait;

    const PARAM_EXPORT_DATA = 'export_data';
    const LABEL = 'label';
    const INSTANCES = 'instances';
    const DESTINATION = 'destination';
    const FILENAME = 'filename';
    const RESOURCE_URI = 'uri';
    const REMOTE_QTI_ITEM_FILESYSTEM_ID = 'remoteQTIItemFilesystem';
    const SHARED_QTI_ITEMS_PATH = 'shared/qti-items/';

    public function __invoke($params)
    {
        try {
            $report = $this->getExporter()->export(
                $params[self::PARAM_EXPORT_DATA],
                $params[self::PARAM_EXPORT_DATA][self::DESTINATION]
            );

            $this->savePackageExternally(
                self::SHARED_QTI_ITEMS_PATH . $params[self::PARAM_EXPORT_DATA][self::FILENAME],
                $report->getData()
            );

            $report->setMessage(__('Resource(s) successfully shared.'));

        } catch (Exception $exception) {

        }

        return $report;
    }

    protected function getExporter(): QtiPackage22ExportHandler
    {
        return new QtiPackage22ExportHandler();
    }

    /**
     * @return object|null
     */
    private function getFileSystem(): FilesystemInterface
    {
        return $this->getServiceManager()
            ->getContainer()
            ->get(FileSystemService::SERVICE_ID)
            ->getFileSystem(self::REMOTE_QTI_ITEM_FILESYSTEM_ID);
    }

    private function savePackageExternally(string $path, string $qtiPackage): void
    {
        if (!$this->getFileSystem()->putStream($path, fopen($qtiPackage, 'r'))) {
            throw new Exception('Could not save the package externally');
        }
    }

    private function getExternalPath(): string
    {
        return sprintf(self::SHARED_QTI_ITEMS_PATH);
    }
}
