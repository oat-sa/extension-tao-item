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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

use oat\tao\model\TaoOntology;
use oat\generis\model\GenerisRdf;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\model\ontology\ItemAuthorRole;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoItems\model\CategoryService;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class taoItems_scripts_update_Updater extends \common_ext_ExtensionUpdater {

    /**
     *
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {


        if ($this->isBetween('0.0.0', '2.14.0')) {
            throw new \common_exception_NotImplemented('Updates from versions prior to Tao 3.1 are not longer supported, please update to Tao 3.1 first');
        }

        $this->skip('2.15.0', '2.22.3');

        if ($this->isVersion('2.22.3')) {

            OntologyUpdater::syncModels();

            $categoryService = new CategoryService();
            $categoryService->setServiceManager($this->getServiceManager());
            $this->getServiceManager()->register(CategoryService::SERVICE_ID, $categoryService);

            $this->setVersion('2.23.0');
        }

        if ($this->isVersion('2.23.0')) {
            OntologyUpdater::syncModels();
            $this->setVersion('2.24.0');
        }

        $this->skip('2.24.0', '5.5.1');

        if ($this->isVersion('5.5.1')) {
            OntologyUpdater::syncModels();

            $this->setVersion('5.6.0');
        }
        $this->skip('5.6.0', '5.9.0');
    }
}
