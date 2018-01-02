<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\taoItems\model\event\ItemImportEvent;
use oat\taoItems\model\task\ImportItemByHandler;
use oat\taoQtiItem\model\import\QtiItemImport;
use oat\taoQtiItem\model\import\QtiPackageImport;
use oat\taoTaskQueue\model\TaskLogActionTrait;

/**
 * This controller provide the actions to import items
 *
 * @author  CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 */
class taoItems_actions_ItemImport extends tao_actions_Import
{
    use TaskLogActionTrait;

    /**
     * overwrite the parent index to add the requiresRight for Items only
     *
     * @requiresRight id WRITE
     * @see           tao_actions_Import::index()
     */
    public function index()
    {
        parent::index();
    }

    /**
     * Uses task queue for only QTI import.
     *
     * @param tao_models_classes_import_ImportHandler $importer
     * @param tao_helpers_form_Form                   $importForm
     * @return string
     * @throws Exception
     * @throws common_exception_NotAcceptable
     */
    protected function handleSubmittedForm(tao_models_classes_import_ImportHandler $importer, tao_helpers_form_Form $importForm)
    {
        // use task for only QTI import
        if ($importer instanceof QtiPackageImport || $importer instanceof QtiItemImport) {
            if (!\tao_helpers_Request::isAjax()) {
                throw new \Exception('Only ajax call allowed.');
            }

            $task = ImportItemByHandler::createTask($importer, $importForm, $this->getCurrentClass());

            return $this->returnTaskJson($task);
        } else {
            return parent::handleSubmittedForm($importer, $importForm);
        }
    }

    /**
     * @return array
     * @throws common_exception_Error
     * @throws common_exception_NoImplementation
     */
    protected function getAvailableImportHandlers()
    {
        $returnValue = parent::getAvailableImportHandlers();

        foreach (array_keys($returnValue) as $key) {
            if ($returnValue[$key] instanceof \tao_models_classes_import_CsvImporter) {
                $importer = new \oat\taoItems\model\CsvImporter();
                $returnValue[$key] = $importer;
            }
        }

        $itemModelClass = new core_kernel_classes_Class(taoItems_models_classes_itemModel::CLASS_URI_MODELS);
        foreach ($itemModelClass->getInstances() as $model) {
            $impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($model);
            if (in_array('tao_models_classes_import_ImportProvider', class_implements($impl))) {
                foreach ($impl->getImportHandlers() as $handler) {
                    array_unshift($returnValue, $handler);
                }
            }
        }


        return $returnValue;
    }

    /**
     * @inheritdoc
     */
    protected function onAfterImport(common_report_Report $report)
    {
        if (common_report_Report::TYPE_SUCCESS == $report->getType()) {
            $this->getEventManager()->trigger(new ItemImportEvent($report));
        }
    }
}
