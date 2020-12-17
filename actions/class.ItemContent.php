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
 * Copyright (c) 2014-2018 (original work) Open Assessment Technologies SA;
 *
 */

use oat\generis\model\OntologyAwareTrait;
use oat\tao\helpers\FileUploadException;
use oat\tao\model\accessControl\data\PermissionException;
use oat\tao\model\http\ContentDetector;
use oat\tao\model\media\MediaBrowser;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use oat\taoItems\model\media\AssetTreeBuilder;
use oat\taoItems\model\media\ItemMediaResolver;
/**
 * Items Content Controller provide access to the files of an item
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
class taoItems_actions_ItemContent extends tao_actions_CommonModule
{
    use OntologyAwareTrait;

    /**
     * @throws common_exception_MissingParameter
     */
    public function files(): void
    {
        $params = $this->getPsrRequest()->getQueryParams();

        if (empty($params['uri'])) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }

        if (empty($params['lang'])) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }

        $itemLang = $params['lang'];
        $path = $params['path'];
        $itemUri = $params['uri'];
        $depth = (int)($params['depth'] ?? 1);
        $childrenOffset = (int)($params['childrenOffset'] ?? AssetTreeBuilder::DEFAULT_PAGINATION_OFFSET);

        $item = $this->getResource($itemUri);
        $filters = $this->buildFilters($params);

        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($path);

        $search = new DirectorySearchQuery($asset, $itemUri, $itemLang, $filters, $depth, $childrenOffset);

        $data = $this->getAssetTreeBuilder()->build($search);

        $this->returnJson($data);
    }

    /**
     * Returns whenever or not a file exists at the indicated path
     *
     * @throws common_exception_MissingParameter
     */
    public function fileExists()
    {
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }

        $item = $this->getResource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');

        try {
            $resolver = new ItemMediaResolver($item, $itemLang);
            $asset = $resolver->resolve($this->getRequestParameter('path'));
            $asset->getMediaSource()->getFileInfo($asset->getMediaIdentifier());
            $found = true;
        } catch (tao_models_classes_FileNotFoundException $exception) {
            $found = false;
        }
        return $this->returnJson(array(
            'exists' => $found
        ));
    }

    /**
     * Upload a file to the item directory
     *
     * @throws common_exception_MissingParameter
     */
    public function upload()
    {
        //as upload may be called multiple times, we remove the session lock as soon as possible
        try{
            session_write_close();
            if ($this->hasRequestParameter('uri')) {
                $itemUri = $this->getRequestParameter('uri');
                $item = $this->getResource($itemUri);
            }

            if ($this->hasRequestParameter('lang')) {
                $itemLang = $this->getRequestParameter('lang');
            }

            if (!$this->hasRequestParameter('path')) {
                throw new common_exception_MissingParameter('path', __METHOD__);
            }

            if (!$this->hasRequestParameter('filters')) {
                throw new common_exception_MissingParameter('filters', __METHOD__);
            }
            $filters = $this->getRequestParameter('filters');

            $resolver = new ItemMediaResolver($item, $itemLang);
            $asset = $resolver->resolve($this->getRequestParameter('relPath'));

            $file = tao_helpers_Http::getUploadedFile('content');
            $fileTmpName = $file['tmp_name'].'_'.$file['name'];

            if (!tao_helpers_File::copy($file['tmp_name'], $fileTmpName)) {
                throw new common_exception_Error('impossible to copy '.$file['tmp_name'].' to '.$fileTmpName);
            }

            $mime = \tao_helpers_File::getMimeType($fileTmpName);
            if (is_string($filters)) {
                // the mime type is part of the $filters
                $filters = explode(',', $filters);
                if ((in_array($mime, $filters))) {
                    $filedata = $asset->getMediaSource()->add($fileTmpName, $file['name'],
                        $asset->getMediaIdentifier());
                } else {
                    throw new FileUploadException(__('The file you tried to upload is not valid'));
                }
            } else {
                $valid = false;
                // OR the extension is part of the filter and it correspond to the mime type
                foreach ($filters as $filter) {
                    if ($filter['mime'] === $mime &&
                        (!isset($filter['extension']) || $filter['extension'] === \tao_helpers_File::getFileExtention($fileTmpName))
                    ) {
                        $valid = true;
                    }
                }
                if ($valid) {
                    $filedata = $asset->getMediaSource()->add($fileTmpName, $file['name'],
                        $asset->getMediaIdentifier());
                } else {
                    throw new FileUploadException(__('The file you tried to upload is not valid'));
                }
            }

            $this->returnJson($filedata);
            return;
        }
        catch(PermissionException $e){
            $message = $e->getMessage();
        }
        catch(FileUploadException $e){
            $message = $e->getMessage();
        }
        catch(common_Exception $e){
            $this->logWarning($e->getMessage());
            $message = _('Unable to upload file');
        }
        $this->returnJson(array('error' => $message));

    }

    /**
     * Download a file to the item directory*
     * @throws common_exception_MissingParameter
     * @throws common_exception_Error
     * @throws tao_models_classes_FileNotFoundException
     */
    public function download()
    {
        $svgzSupport = false;
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }

        if($this->hasRequestParameter('svgzsupport')){
            $svgzSupport = true;
        }

        $item = $this->getResource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');

        $resolver = new ItemMediaResolver($item, $itemLang);

        $rawParams = $this->getRequest()->getRawParameters();//have to use raw value to respect special characters in names
        $asset = $resolver->resolve($rawParams['path']);
        $filePath = $asset->getMediaSource()->download($asset->getMediaIdentifier());

        $info = $asset->getMediaSource()->getFileInfo($asset->getMediaIdentifier());

        if ($info['mime'] != 'application/qti+xml') {
            header('Content-Type: ' . $info['mime']);
        }

        \tao_helpers_Http::returnFile($filePath, false, $svgzSupport);
    }

    /**
     * Delete a file from the item directory
     *
     * @throws common_exception_MissingParameter
     */
    public function delete()
    {
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }

        $item = $this->getResource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');

        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($this->getRequestParameter('path'));
        $deleted = $asset->getMediaSource()->delete($asset->getMediaIdentifier());

        return $this->returnJson(array('deleted' => $deleted));
    }

    /**
     * Get the media source based on the partial url
     *
     * @param string $urlPrefix
     * @param core_kernel_classes_resource $item
     * @param string $itemLang
     * @return \oat\tao\model\media\MediaBrowser
     */
    protected function getMediaSource($urlPrefix, $item, $itemLang)
    {
        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($urlPrefix);
        return $asset->getMediaSource();
    }

    private function buildFilters(array $params): array
    {
        $filters = [];
        if (isset($params['filters'])) {
            $filterParameter = $params['filters'];
            if (is_array($filterParameter)) {
                foreach ($filterParameter as $filter) {
                    if (preg_match('/\/\*/', $filter['mime'])) {
                        $this->logWarning('Stars mime type are not yet supported, filter "' . $filter['mime'] . '" will fail');
                    }
                    $filters[] = $filter['mime'];
                }
            } else {
                if (preg_match('/\/\*/', $filterParameter)) {
                    $this->logWarning('Stars mime type are not yet supported, filter "' . $filterParameter . '" will fail');
                }
                $filters = array_map('trim', explode(',', $filterParameter));
            }
        }
        return $filters;
    }

    private function getContentDetector(): ContentDetector
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(ContentDetector::class);
    }

    /** @noinspection PhpIncompatibleReturnTypeInspection */
    private function getAssetTreeBuilder(): AssetTreeBuilder
    {
        return  $this->getServiceLocator()->get(AssetTreeBuilder::SERVICE_ID);
    }

}
