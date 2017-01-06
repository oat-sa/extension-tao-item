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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2016 (update and modification) Open Assessment Technologies SA;
 */

namespace oat\taoItems\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\oatbox\filesystem\FileSystemService;

/**
 * Register the category service
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class CreateItemDirectory extends InstallAction
{
    /**
     * @param $params
     */
    public function __invoke($params)
    {
        $fsService = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);
        if (!$fsService->hasDirectory('itemDirectory')) {
            $source = $fsService->createFileSystem('itemDirectory', 'taoItems/itemData');
            $this->registerService(FileSystemService::SERVICE_ID, $fsService);
        }
        \taoItems_models_classes_ItemsService::singleton()->setDefaultFilesourceId('itemDirectory');
    }
}
