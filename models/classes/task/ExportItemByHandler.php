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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoItems\model\task;

use common_report_Report as Report;
use oat\oatbox\event\EventManager;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoItems\model\event\ItemExportEvent;
use oat\taoTaskQueue\model\QueueDispatcherInterface;

/**
 * Export item by a tao_models_classes_export_ExportHandler
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class ExportItemByHandler extends AbstractAction
{
    const PARAM_EXPORT_HANDLER = 'export_handler';
    const PARAM_EXPORT_DATA = 'export_data';
    const PARAM_EXPORT_SELECTED_RESOURCE_URI = 'selected_resource';

    public function __invoke($params)
    {
        if (!isset($params[self::PARAM_EXPORT_HANDLER]) || !class_exists($params[self::PARAM_EXPORT_HANDLER])) {
            throw new \InvalidArgumentException('Please provide a valid export handler');
        }

        if (!isset($params[self::PARAM_EXPORT_DATA]) || !is_array($params[self::PARAM_EXPORT_DATA])) {
            throw new \InvalidArgumentException('Please provide the export data as array');
        }

        if (!isset($params[self::PARAM_EXPORT_SELECTED_RESOURCE_URI])) {
            throw new \InvalidArgumentException('Please provide the selected resource uri.');
        }

        /** @var \tao_models_classes_export_ExportHandler $exporter */
        $exporter = new $params[self::PARAM_EXPORT_HANDLER];

        $this->propagate($exporter);

        try {
            $report = $exporter->export($params[self::PARAM_EXPORT_DATA], \tao_helpers_Export::getExportPath());

            if ($report instanceof Report) {
                $filePath = $report->getData();
            } else {
                $filePath = $report;
                $report = Report::createSuccess(__('Successful export of resource(s)'));
            }

            // if $filePath it's a valid url, it means, it is already stored in the file system somewhere
            // so no need to copy the file under queue storage
            if ($filePath && !filter_var($filePath, FILTER_VALIDATE_URL)) {
                $newFileName = 'exportByHandler/' . basename($filePath);

                /** @var Directory $queueStorage */
                $queueStorage = $this->getServiceLocator()
                    ->get(FileSystemService::SERVICE_ID)
                    ->getDirectory(QueueDispatcherInterface::FILE_SYSTEM_ID);

                // Save the generated local file into the queue storage
                $file = $queueStorage->getFile($newFileName);
                $stream = fopen($filePath, 'r');
                $file->put($stream);
                fclose($stream);

                // set the new file name stored
                $report->setData($newFileName);

                // delete tmp file
                unlink($filePath);
            }

            $this->onExport($params[self::PARAM_EXPORT_SELECTED_RESOURCE_URI]);

        } catch (\common_exception_UserReadableException $e) {
            $report = Report::createFailure($e->getUserMessage());
        }

        return $report;
    }

    /**
     * @param string $selectedResourceUri
     */
    protected function onExport($selectedResourceUri)
    {
        $this->getServiceLocator()
            ->get(EventManager::SERVICE_ID)
            ->trigger(new ItemExportEvent($selectedResourceUri));
    }
}