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
use oat\oatbox\service\ServiceManager;
use oat\tao\model\upload\UploadService;
use oat\taoItems\model\event\ItemImportEvent;
use oat\taoTaskQueue\model\QueueDispatcher;
use oat\taoTaskQueue\model\Task\CallbackTaskInterface;

/**
 * Import item by a tao_models_classes_import_ImportHandler
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class ImportItemByHandler extends AbstractAction
{
    const PARAM_IMPORT_HANDLER = 'import_handler';
    const PARAM_FORM_VALUES = 'form_values';
    const PARAM_PARENT_CLASS = 'parent_class_uri';

    /**
     * @param array $params
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        if (!isset($params[self::PARAM_IMPORT_HANDLER]) || !class_exists($params[self::PARAM_IMPORT_HANDLER])) {
            throw new \InvalidArgumentException('Please provide a valid import handler');
        }

        /** @var \tao_models_classes_import_ImportHandler $importer */
        $importer = new $params[self::PARAM_IMPORT_HANDLER];

        /** @var Report $report */
        $report = $importer->import(new \core_kernel_classes_Class($params[self::PARAM_PARENT_CLASS]), $params[self::PARAM_FORM_VALUES]);

        $this->onAfterImport($report);

        return $report;
    }

    /**
     * @param Report $report
     */
    protected function onAfterImport(Report $report)
    {
        if (Report::TYPE_SUCCESS == $report->getType()) {
            $this->getServiceLocator()->get(EventManager::SERVICE_ID)->trigger(new ItemImportEvent($report));
        }
    }

    /**
     * @param \tao_models_classes_import_ImportHandler $importer
     * @param \tao_helpers_form_Form                   $importForm
     * @param \core_kernel_classes_Class               $parentClass
     * @return CallbackTaskInterface
     * @throws \common_exception_NotAcceptable
     *
     * TODO: use a common service to create this task instead of static method and avoid usage of "ServiceManager::getServiceManager()"
     */
    public static function createTask(\tao_models_classes_import_ImportHandler $importer, \tao_helpers_form_Form $importForm, \core_kernel_classes_Class $parentClass)
    {
        /** @var  UploadService $uploadService */
        $uploadService = ServiceManager::getServiceManager()->get(UploadService::SERVICE_ID);
        $file = $uploadService->getUploadedFlyFile($importForm->getValue('source')['uploaded_file']);

        $formValues = [
            'fly_file' => $file->getPrefix(),
        ];

        if ($importForm->hasElement('rollback')) {
            $formValues['rollback'] = $importForm->getValue('rollback');
        }

        /** @var QueueDispatcher $queueDispatcher */
        $queueDispatcher = ServiceManager::getServiceManager()->get(QueueDispatcher::SERVICE_ID);

        return $queueDispatcher->createTask(
            new static(),
            [
                self::PARAM_IMPORT_HANDLER => get_class($importer),
                self::PARAM_FORM_VALUES => $formValues,
                self::PARAM_PARENT_CLASS => $parentClass->getUri()
            ],
            __('Import a "%s" into "%s"', $importer->getLabel(), $parentClass->getLabel())
        );
    }
}