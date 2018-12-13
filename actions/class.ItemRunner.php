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
 * Copyright (c) 2013-2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\tao\model\state\StateStorage;

/**
 * This module runs the items
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems

 */
class taoItems_actions_ItemRunner extends tao_actions_ServiceModule
{
	public function index()
    {
		$userId = $this->getSession()->getUserUri();
		if(is_null($userId)){
			throw new common_exception_Error('No user is logged in');
		}
		$lang = $this->getSession()->getDataLanguage();

		if ($this->hasRequestParameter('serviceCallId')) {
                    $serviceCallId = $this->getRequestParameter('serviceCallId');
                    $variableData = $this->getServiceLocator()->get(StateStorage::SERVICE_ID)->get($userId, $serviceCallId);
                    $this->setData('storageData', array(
                            'serial'	=> $serviceCallId,
                            'data'		=> is_null($variableData) ? array() : $variableData
                    ));
		}

		$directory = $this->getDirectory($this->getRequestParameter('itemPath'));

		if (!$directory->has($lang) && $directory->has(DEFAULT_LANG)) {
		    $lang = DEFAULT_LANG;
		}

		$this->setData('itemPath', $directory->getPublicAccessUrl().$lang.'/index.html');
		$this->setData('itemId', $this->getRequestParameter('itemUri'));
		$this->setData('resultServerEndpoint', $this->getResultServerEndpoint());
        $this->setData('resultServerParams', $this->getResultServerParams());
        $this->setData('client_timeout', $this->getClientTimeout());
        $this->setData('client_config_url', $this->getClientConfigUrl());

		$this->selectView();
	}

	/**
	 * The implementation of this method calls ItemRunner::setView in order to
	 * select the view to be displayed.
	 */
	protected function selectView()
    {
	    $this->setView('runtime/item_runner.tpl', 'taoItems');
	}

    /**
     * Get the URL of the result server
     * @return string
     */
    protected function getResultServerEndpoint()
    {
        return _url('', 'ResultServerStateFull', 'taoResultServer');
    }

    /**
     * Get extra parameters to give the result server
     * @return array an assoc array of additional parameters
     */
    protected function getResultServerParams()
    {
        return array();
    }
}
