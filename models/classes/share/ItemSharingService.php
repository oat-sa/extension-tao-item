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

use common_exception_Error;
use Exception;
use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use common_report_Report as Report;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoQtiItem\model\Export\QtiPackage22ExportHandler;
use oat\taoQtiItem\model\qti\exception\ExportException;

class ItemSharingService
{
    const PARAM_EXPORT_DATA = 'export_data';
    const LABEL = 'label';
    const INSTANCES = 'instances';
    const DESTINATION = 'destination';
    const FILENAME = 'filename';
    const RESOURCE_URI = 'uri';
    const REMOTE_QTI_ITEM_FILESYSTEM_ID = 'remoteQTIItemFilesystem';
    const SHARED_QTI_ITEMS_PATH = 'shared/qti-items/';
    private $exportHandler;
    private $fileSystem;

    public function __construct(QtiPackage22ExportHandler $exportHandler, FileSystemService $fileSystem)
    {
        $this->exportHandler = $exportHandler;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @throws common_exception_Error
     * @throws Exception
     */
    public function shareItems(array $params): Report
    {
        $this->validateParams($params);
        $report = $this->exportHandler->export(
            $params[self::PARAM_EXPORT_DATA],
            $params[self::PARAM_EXPORT_DATA][self::DESTINATION]
        );

        $this->savePackageExternally(
            self::SHARED_QTI_ITEMS_PATH . $params[self::PARAM_EXPORT_DATA][self::FILENAME] . '.zip',
            $report->getData()
        );

        return $report;
    }
    private function savePackageExternally(string $path, string $qtiPackage): void
    {
        if (!$this->getFileSystem()->putStream($path, fopen($qtiPackage, 'r'))) {
            throw new Exception('Could not save the package externally');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateParams($params): void
    {
        if (
            !isset($params[self::PARAM_EXPORT_DATA])
            || !is_array($params[self::PARAM_EXPORT_DATA])
            || !isset($params[self::PARAM_EXPORT_DATA][self::LABEL])
            || !isset($params[self::PARAM_EXPORT_DATA][self::INSTANCES])
            || !isset($params[self::PARAM_EXPORT_DATA][self::DESTINATION])
            || !isset($params[self::PARAM_EXPORT_DATA][self::FILENAME])
            || !isset($params[self::PARAM_EXPORT_DATA][self::RESOURCE_URI])
        ) {
            throw new InvalidArgumentException('Please provide correct export data');
        }
    }

    private function getFileSystem(): FilesystemInterface
    {
        return $this->fileSystem->getFileSystem(self::REMOTE_QTI_ITEM_FILESYSTEM_ID);
    }
}
