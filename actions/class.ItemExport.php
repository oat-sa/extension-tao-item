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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2025 (update and modification) Open Assessment Technologies SA;
 */

use oat\tao\model\featureFlag\FeatureFlagChecker;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use tao_models_classes_export_ExportHandler as ExportHandlerInterface;

/**
 * This controller provide the actions to export items
 *
 * @author  CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */
class taoItems_actions_ItemExport extends tao_actions_Export
{
    private const FEATURE_FLAG_QTI3_EXPORT = 'FEATURE_FLAG_QTI3_EXPORT';

    /**
     * overwrite the parent index to add the requiresRight for Items only
     *
     * @requiresRight id READ
     * @throws common_Exception
     * @see           tao_actions_Import::index()
     */
    public function index()
    {
        parent::index();
    }

    protected function getAvailableExportHandlers(): array
    {
        $returnValue = parent::getAvailableExportHandlers();

        $itemModelClass = $this->getClass(taoItems_models_classes_itemModel::CLASS_URI_MODELS);
        $itemModels = $itemModelClass->getInstances();
        foreach ($itemModels as $model) {
            $impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($model);
            if (in_array('tao_models_classes_export_ExportProvider', class_implements($impl), true)) {
                foreach ($impl->getExportHandlers() as $handler) {
                    if ($this->isHandlerEnabled($handler)) {
                        array_unshift($returnValue, $handler);
                    }
                }
            }
        }

        $instances = $this->getClassInstances();
        if (!count($instances)) {
            $returnValue = array_filter(
                $returnValue,
                static function (ExportHandlerInterface $handler) {
                    return $handler instanceof tao_models_classes_export_RdfExporter;
                }
            );
        }


        return $returnValue;
    }

    public function getClassInstances(): array
    {
        $instances = [];
        $classUri = $this->hasPostParameter('classUri') ? $this->getPostParameter('classUri') : '';
        if ($classUri) {
            $class = $this->getClass(tao_helpers_Uri::decode($classUri));
            $instances = $class->getInstances(true);
        }
        return $instances;
    }

    protected function getFormFactory(
        array $handlers,
        ExportHandlerInterface $exporter,
        core_kernel_classes_Resource $selectedResource,
        array $formData
    ): tao_actions_form_Export {
        $formFactory = parent::getFormFactory($handlers, $exporter, $selectedResource, $formData);
        $instances = $this->getClassInstances();
        if (!count($instances)) {
            $formFactory->setInfoBox(
                __('<b>Note</b>: For empty classes, the RDF format<br />is the only available format.')
            );
        }
        return $formFactory;
    }

    /**
     * Function returns items to export.
     * Items that has no content (<b>QTI items</b> without <i>qti.xml</i> file or empty <b>Open Web Items</b>) will be
     * filtered
     *
     * @return core_kernel_classes_Resource[] An array of items.
     */
    protected function getResourcesToExport()
    {
        $resources = parent::getResourcesToExport();
        $service = taoItems_models_classes_ItemsService::singleton();

        $resources = array_filter($resources, function ($val) use ($service) {
            return $service->hasItemContent($val);
        });

        return $resources;
    }

    /**
     * TODO: This was created only to temporary handle QTI3 Export feature. Will be removed.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function isHandlerEnabled(ExportHandlerInterface $handler): bool
    {
        if (
            !$this->getPsrContainer()->get(FeatureFlagChecker::class)->isEnabled(self::FEATURE_FLAG_QTI3_EXPORT)
            && $handler instanceof oat\taoQtiItem\model\Export\Qti3Package\Handler
        ) {
            return false;
        }

        return true;
    }
}
