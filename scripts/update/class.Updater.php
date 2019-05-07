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

use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoItems\model\CategoryService;
use oat\taoItems\model\render\NoneItemReplacement;
use oat\taoItems\model\render\ItemAssetsReplacement;
use oat\taoItems\model\preview\ItemPreviewerService;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class taoItems_scripts_update_Updater extends \common_ext_ExtensionUpdater {

    /**
     * @param $initialVersion
     * @return string|void
     * @throws common_Exception
     */
    public function update($initialVersion) {


        if ($this->isBetween('0.0.0', '2.8.0')) {
            throw new \common_exception_NotImplemented('Updates from versions prior to Tao 3.1 are not longer supported, please update to Tao 3.1 first');
        }

        $this->skip('2.8.1', '2.22.3');

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

        if ($this->isVersion('5.9.0')) {
            AclProxy::applyRule(new AccessRule('grant', \oat\tao\model\user\TaoRoles::REST_PUBLISHER, array('ext'=>'taoItems', 'mod' => 'RestItems')));
            AclProxy::applyRule(new AccessRule('grant', \oat\tao\model\user\TaoRoles::REST_PUBLISHER, array('ext'=>'taoItems', 'mod' => 'RestFormItem')));
            $this->setVersion('5.10.0');
        }

        if ($this->isVersion('5.10.0')) {
            $replacementService = new NoneItemReplacement();
            $this->getServiceManager()->register(ItemAssetsReplacement::SERVICE_ID, $replacementService);

            $this->setVersion('5.11.0');
        }

        $this->skip('5.11.0', '5.12.2');

        if ($this->isVersion('5.12.2')) {
            $itemPreviewerService = new ItemPreviewerService();
            $this->getServiceManager()->register(ItemPreviewerService::SERVICE_ID, $itemPreviewerService);

            $this->setVersion('5.13.0');
        }

        $this->skip('5.13.0', '6.0.0');

        if ($this->isVersion('6.0.0')) {
            OntologyUpdater::syncModels();
            $this->setVersion('6.1.0');
        }

        $this->skip('6.1.0', '6.6.3');
    }
}
