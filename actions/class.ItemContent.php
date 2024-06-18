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
 * Copyright (c) 2014-2021 (original work) Open Assessment Technologies SA;
 *
 */

use oat\generis\model\OntologyAwareTrait;
use oat\tao\helpers\FileUploadException;
use oat\tao\model\accessControl\Context;
use oat\tao\model\accessControl\data\PermissionException;
use oat\tao\model\accessControl\PermissionChecker;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\http\HttpJsonResponseTrait;
use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\MediaBrowser;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;
use oat\tao\model\media\ProcessedFileStreamAware;
use oat\tao\model\media\TaoMediaException;
use oat\tao\model\resources\ResourceAccessDeniedException;
use oat\taoItems\model\media\AssetTreeBuilder;
use oat\taoItems\model\media\AssetTreeBuilderInterface;
use oat\taoItems\model\media\ItemMediaResolver;
use oat\taoItems\model\media\LocalItemSource;
use Psr\Http\Message\StreamInterface;
use common_exception_MissingParameter as MissingParameterException;
use tao_models_classes_FileNotFoundException as FileNotFoundException;

/**
 * Items Content Controller provide access to the files of an item
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
class taoItems_actions_ItemContent extends tao_actions_CommonModule
{
    use HttpJsonResponseTrait;
    use OntologyAwareTrait;

    /**
     * @throws MissingParameterException|TaoMediaException
     */
    public function files(): void
    {
        $params = $this->getRequiredQueryParams('uri', 'lang', 'path');
        ['uri' => $uri, 'lang' => $lang, 'path' => $path] = $params;

        $depth = (int)($params['depth'] ?? 1);
        $childrenOffset = (int)($params['childrenOffset'] ?? AssetTreeBuilder::DEFAULT_PAGINATION_OFFSET);

        $filters = $this->buildFilters($params);

        $searchQuery = new DirectorySearchQuery(
            $this->resolveAsset($uri, $path, $lang),
            $uri,
            $lang,
            $filters,
            $depth,
            $childrenOffset
        );

        $this->setSuccessJsonResponse($this->getAssetTreeBuilder()->build($searchQuery));
    }

    /**
     * Returns whenever or not a file exists at the indicated path
     * @throws MissingParameterException|TaoMediaException
     */
    public function fileExists(): void
    {
        try {
            $params = $this->getRequiredQueryParams('uri', 'lang', 'path');
            $asset = $this->resolveAsset($params['uri'], $params['path'], $params['lang']);

            $asset->getMediaSource()->getFileInfo($asset->getMediaIdentifier());
            $found = true;
        } catch (FileNotFoundException $exception) {
            $found = false;
        }

        $formatter = $this->getResponseFormatter()
            ->withJsonHeader()
            ->withBody(['exists' => $found]);
        $this->setResponse($formatter->format($this->getPsrResponse()));
    }

    /**
     * Upload a file to the item directory
     */
    public function upload(): void
    {
        $formatter = $this->getResponseFormatter()
            ->withJsonHeader();

        //as upload may be called multiple times, we remove the session lock as soon as possible
        try {
            session_write_close();

            $params = $this->getRequiredQueryParams('uri', 'lang', 'relPath', 'filters');
            ['filters' => $filters] = $params;
            $asset = $this->getMediaAsset('uploadAsset');

            $file = tao_helpers_Http::getUploadedFile('content');
            $fileTmpName = $file['tmp_name'] . '_' . $file['name'];

            if (!tao_helpers_File::copy($file['tmp_name'], $fileTmpName)) {
                throw new common_exception_Error('impossible to copy ' . $file['tmp_name'] . ' to ' . $fileTmpName);
            }

            $mime = tao_helpers_File::getMimeType($fileTmpName);
            if (is_string($filters)) {
                // the mime type is part of the $filters
                $filters = explode(',', $filters);
                if ((in_array($mime, $filters))) {
                    $fileData = $asset->getMediaSource()->add(
                        $fileTmpName,
                        $file['name'],
                        $asset->getMediaIdentifier()
                    );
                } else {
                    throw new FileUploadException(__('The file you tried to upload is not valid'));
                }
            } else {
                $valid = false;
                // OR the extension is part of the filter and it correspond to the mime type
                $fileExtension = tao_helpers_File::getFileExtention($fileTmpName);
                foreach ($filters as $filter) {
                    if (
                        $filter['mime'] === $mime &&
                        (!isset($filter['extension']) || $filter['extension'] === $fileExtension)
                    ) {
                        $valid = true;
                    }
                }
                if ($valid) {
                    $fileData = $asset->getMediaSource()->add(
                        $fileTmpName,
                        $file['name'],
                        $asset->getMediaIdentifier()
                    );
                } else {
                    throw new FileUploadException(__('The file you tried to upload is not valid'));
                }
            }

            $formatter->withBody($fileData);
        } catch (PermissionException | FileUploadException $e) {
            $formatter->withBody(['error' => $e->getMessage()]);
        } catch (common_Exception $e) {
            $this->logWarning($e->getMessage());
            $formatter->withBody(['error' => __('Unable to upload file')]);
        }

        $this->setResponse($formatter->format($this->getPsrResponse()));
    }

    /**
     * @throws MissingParameterException|FileNotFoundException|TaoMediaException
     */
    public function download(): void
    {
        $asset = $this->getMediaAsset('previewAsset');

        $mediaSource = $asset->getMediaSource();
        $stream = $this->getMediaSourceFileStream($mediaSource, $asset);

        $info = $mediaSource->getFileInfo($asset->getMediaIdentifier());
        $mime = $info['mime'] !== 'application/qti+xml' ? $info['mime'] : null;

        tao_helpers_Http::returnStream($stream, $mime, $this->getPsrRequest());
    }

    /**
     * Delete a file from the item directory
     *
     * @throws MissingParameterException|TaoMediaException
     */
    public function delete(): void
    {
        $asset = $this->getMediaAsset('deleteAsset');

        $deleted = $asset->getMediaSource()->delete($asset->getMediaIdentifier());

        $formatter = $this->getResponseFormatter()
            ->withJsonHeader()
            ->withBody(['deleted' => $deleted]);
        $this->setResponse($formatter->format($this->getPsrResponse()));
    }

    private function getMediaAsset(string $action): ?MediaAsset
    {
        $isWrite = in_array($action, ['deleteAsset', 'uploadAsset'], true);
        $params = $this->getRequiredQueryParams('uri', 'lang');
        $queryParams = $this->getPsrRequest()->getQueryParams();
        $path = $queryParams['relPath'] ?? $queryParams['path'] ?? null;
        $asset = $this->resolveAsset($params['uri'], $path, $params['lang']);
        $resourceUri = $params['uri'];
        $hasAccess = true;

        // We do not want to validate access for item gallery
        if (!$asset->getMediaSource() instanceof LocalItemSource) {
            $resourceUri = tao_helpers_Uri::decode($asset->getMediaIdentifier());
            $context = new Context(
                [
                    Context::PARAM_CONTROLLER => taoItems_actions_ItemContent::class,
                    Context::PARAM_ACTION => $action,
                ]
            );
            $hasAccess = $isWrite ? $this->hasWriteAccessByContext($context) : $this->hasReadAccessByContext($context);
        }

        if (!$isWrite) {
            $hasAccess = $hasAccess && $this->getPermissionChecker()->hasReadAccess($resourceUri);
        }

        if ($isWrite) {
            $hasAccess = $hasAccess && $this->getPermissionChecker()->hasWriteAccess($resourceUri);
        }

        if (!$hasAccess) {
            throw new ResourceAccessDeniedException($resourceUri);
        }

        return $asset;
    }

    /**
     * @throws TaoMediaException
     */
    private function resolveAsset(string $url, string $path, string $lang): MediaAsset
    {
        $item = $this->getResource($url);
        $resolver = new ItemMediaResolver($item, $lang);

        return $resolver->resolve($path);
    }

    /**
     * Returns Query params if mandatory params not empty
     *
     * @throws MissingParameterException
     */
    private function getRequiredQueryParams(string ...$requiredKeys): array
    {
        $params = $this->getPsrRequest()->getQueryParams();
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $params) || empty($params[$key])) {
                throw new MissingParameterException($key, __METHOD__);
            }
        }

        return $params;
    }

    private function buildFilters(array $params): array
    {
        $filters = [];
        if (isset($params['filters'])) {
            $filterParameter = $params['filters'];
            if (is_array($filterParameter)) {
                foreach ($filterParameter as $filter) {
                    if (preg_match('/\/\*/', $filter['mime'])) {
                        $this->logWarning(
                            'Stars mime type are not yet supported, filter "' . $filter['mime'] . '" will fail'
                        );
                    }
                    $filters[] = $filter['mime'];
                }
            } else {
                if (preg_match('/\/\*/', $filterParameter)) {
                    $this->logWarning(
                        'Stars mime type are not yet supported, filter "' . $filterParameter . '" will fail'
                    );
                }
                $filters = array_map('trim', explode(',', $filterParameter));
            }
        }
        return $filters;
    }

    private function getAssetTreeBuilder(): AssetTreeBuilderInterface
    {
        return $this->getServiceLocator()->get(AssetTreeBuilder::SERVICE_ID);
    }

    /**
     * @throws FileNotFoundException
     */
    private function getMediaSourceFileStream(MediaBrowser $mediaSource, MediaAsset $asset): StreamInterface
    {
        if ($mediaSource instanceof ProcessedFileStreamAware) {
            return $mediaSource->getProcessedFileStream($asset->getMediaIdentifier());
        }

        return $mediaSource->getFileStream($asset->getMediaIdentifier());
    }

    private function getPermissionChecker(): PermissionCheckerInterface
    {
        return $this->getServiceLocator()->get(PermissionChecker::class);
    }
}
