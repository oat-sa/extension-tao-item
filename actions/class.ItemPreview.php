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
 *               2013-2018(update and modification) Open Assessment Technologies SA;
 */

use oat\tao\helpers\Base64;
use oat\generis\model\OntologyAwareTrait;
use oat\taoItems\model\media\ItemMediaResolver;
use oat\tao\model\media\sourceStrategy\HttpSource;
use oat\taoItems\model\preview\OntologyItemNotFoundException;

use function GuzzleHttp\Psr7\stream_for;

/**
 * Preview API
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_ItemPreview extends tao_actions_CommonModule
{
    use OntologyAwareTrait;

    public function forwardMe()
    {
        $item = $this->getResource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
        $lang = DEFAULT_LANG;
        $previewUrl = taoItems_models_classes_ItemsService::singleton()->getPreviewUrl($item, $lang);

        if (null === $previewUrl) {
            throw new OntologyItemNotFoundException();
        }

        $this->forwardUrl($previewUrl);
    }

    /**
     * @requiresRight uri READ
     */
    public function index()
    {
        $item = $this->getResource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

        $itemService = taoItems_models_classes_ItemsService::singleton();
        if ($itemService->hasItemContent($item) && $itemService->isItemModelDefined($item)) {
            //this is this url that will contains the preview
            //@see taoItems_actions_LegacyPreviewApi
            $previewUrl = $this->getPreviewUrl($item);

            $this->setData('previewUrl', $previewUrl);
            $this->setData('client_config_url', $this->getClientConfigUrl());
            $this->setData('resultServer', $this->getResultServer());
        }

        $this->setData('state', html_entity_decode($this->getRequestParameter('state')));

        $this->setView('ItemPreview/index.tpl', 'taoItems');
    }

    protected function getPreviewUrl($item, $options = [])
    {
        $code = base64_encode($item->getUri());
        return _url('render/' . $code . '/index', 'ItemPreview', 'taoItems', $options);
    }

    public function render()
    {
        $relPath = tao_helpers_Request::getRelativeUrl();
        [$extension, $module, $action, $codedUri, $path] = explode('/', $relPath, 5);

        $path = rawurldecode($path);
        $uri = base64_decode($codedUri);
        if (!common_Utils::isUri($uri)) {
            throw new common_exception_BadRequest('"' . $codedUri . '" does not decode to a valid item URI');
        }
        $item = $this->getResource($uri);
        if ($path === 'index') {
            $this->renderItem($item);
        } else {
            $this->renderResource($item, $path);
        }
    }

    protected function getRenderedItem($item)
    {
        $itemModel = $item->getOnePropertyValue($this->getProperty(taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL));
        $impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($itemModel);
        if (is_null($impl)) {
            throw new common_Exception('preview not supported for this item type ' . $itemModel->getUri());
        }
        return $impl->render($item, '');
    }

    /**
     * Add the rendered item to psr7 response
     *
     * @param $item
     * @throws common_Exception
     */
    private function renderItem($item)
    {
        $this->response = $this->response->withBody(stream_for($this->getRenderedItem($item)));
    }

    /**
     * @param $item
     * @param $path
     *
     * @throws common_Exception
     * @throws common_exception_Error
     * @throws tao_models_classes_FileNotFoundException
     */
    private function renderResource($item, $path)
    {
        $lang = $this->getSession()->getDataLanguage();
        $resolver = new ItemMediaResolver($item, $lang);
        $asset = $resolver->resolve($path);
        $mediaSource = $asset->getMediaSource();
        $mediaIdentifier = $asset->getMediaIdentifier();

        if ($mediaSource instanceof HttpSource || Base64::isEncodedImage($mediaIdentifier)) {
            throw new common_Exception('Only tao files available for rendering through item preview');
        }

        $info = $mediaSource->getFileInfo($mediaIdentifier);
        $stream = $mediaSource->getFileStream($mediaIdentifier);
        \tao_helpers_Http::returnStream($stream, $info['mime']);
    }

    /**
     * Get the ResultServer API call to be used by the item.
     *
     * @return string A string representing JavaScript instructions.
     */
    protected function getResultServer()
    {
        return [
            'module' => 'taoItems/runtime/ConsoleResultServer'
        ];
    }
}
