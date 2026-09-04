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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

use oat\tao\model\http\HttpJsonResponseTrait;
use oat\taoItems\model\Comment\ItemCommentService;

/**
 * Authoring comments REST API (FR1) — Item, Test, or Asset via resourceType.
 *
 * Routes:
 * - GET  /taoItems/RestResourceComments/index?resourceUri=&resourceType=
 * - POST /taoItems/RestResourceComments/index  (resourceUri, resourceType, body)
 * - POST /taoItems/RestResourceComments/update (id, body) — author can edit own comment
 * - POST /taoItems/RestResourceComments/resolve (id, resolved) — any authenticated authoring user
 * - POST /taoItems/RestResourceComments/delete (id) — author can delete own comment
 *
 * Mention user search lives in user domain:
 * - GET /tao/RestUser/searchUsers?resourceUri=&resourceType=&q=&limit=&offset=
 */
class taoItems_actions_RestResourceComments extends tao_actions_CommonModule
{
    use HttpJsonResponseTrait;

    public function index(): void
    {
        try {
            if ($this->isGetRequest()) {
                $query = $this->getPsrRequest()->getQueryParams();
                $resourceUri = $this->requireStringParam($query['resourceUri'] ?? null, 'resourceUri');
                if ($resourceUri === null) {
                    return;
                }

                $resourceType = $this->requireStringParam($query['resourceType'] ?? null, 'resourceType');
                if ($resourceType === null) {
                    return;
                }

                $this->setSuccessJsonResponse(
                    $this->getItemCommentService()->list($resourceUri, $resourceType)
                );

                return;
            }

            if ($this->isPostRequest()) {
                $payload = $this->getRequestPayload();
                $resourceUri = $this->requireStringParam($payload['resourceUri'] ?? null, 'resourceUri');
                if ($resourceUri === null) {
                    return;
                }

                $resourceType = $this->requireStringParam($payload['resourceType'] ?? null, 'resourceType');
                if ($resourceType === null) {
                    return;
                }

                $body = $this->requireStringParam($payload['body'] ?? null, 'body');
                if ($body === null) {
                    return;
                }

                $comment = $this->getItemCommentService()->create($resourceUri, $resourceType, $body);
                $this->setSuccessJsonResponse(
                    $this->getItemCommentService()->serializeComment($comment),
                    201
                );

                return;
            }

            $this->setErrorJsonResponse('Method not allowed', 405, [], 405);
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to process resource comments request', 500, [], 500);
        }
    }

    public function update(): void
    {
        try {
            if (!$this->isPostRequest() && !$this->isPutRequest()) {
                $this->setErrorJsonResponse('Method not allowed', 405, [], 405);

                return;
            }

            $payload = $this->getRequestPayload();
            $commentId = $this->requireStringParam($payload['id'] ?? null, 'id');
            if ($commentId === null) {
                return;
            }

            $body = $this->requireStringParam($payload['body'] ?? null, 'body');
            if ($body === null) {
                return;
            }

            $service = $this->getItemCommentService();
            $comment = $service->update($commentId, $body);
            $this->setSuccessJsonResponse($service->serializeComment($comment));
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to update item comment', 500, [], 500);
        }
    }

    public function resolve(): void
    {
        try {
            if (!$this->isPostRequest() && !$this->isPutRequest()) {
                $this->setErrorJsonResponse('Method not allowed', 405, [], 405);

                return;
            }

            $payload = $this->getRequestPayload();
            $commentId = $this->requireStringParam($payload['id'] ?? null, 'id');
            if ($commentId === null) {
                return;
            }

            if (!array_key_exists('resolved', $payload)) {
                throw new InvalidArgumentException('resolved is required');
            }

            $resolved = $this->parseBoolStrict($payload['resolved']);
            if ($resolved === null) {
                $this->setErrorJsonResponse('resolved must be a boolean (true, false, 1, 0)', 400, [], 400);

                return;
            }

            $service = $this->getItemCommentService();
            $comment = $service->resolve($commentId, $resolved);
            $this->setSuccessJsonResponse($service->serializeComment($comment));
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to resolve item comment', 500, [], 500);
        }
    }

    public function delete(): void
    {
        try {
            if (!$this->isPostRequest() && !$this->isDeleteRequest()) {
                $this->setErrorJsonResponse('Method not allowed', 405, [], 405);

                return;
            }

            $payload = $this->getRequestPayload();
            $commentId = $this->requireStringParam($payload['id'] ?? null, 'id');
            if ($commentId === null) {
                return;
            }

            $this->getItemCommentService()->delete($commentId);
            $this->setSuccessJsonResponse(['id' => $commentId]);
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to delete item comment', 500, [], 500);
        }
    }

    /**
     * @param mixed $value
     */
    private function requireStringParam($value, string $name): ?string
    {
        if ($value === null) {
            $this->setErrorJsonResponse(sprintf('%s is required', $name), 400, [], 400);

            return null;
        }

        if (!is_string($value)) {
            $this->setErrorJsonResponse(sprintf('%s must be a string', $name), 400, [], 400);

            return null;
        }

        return $value;
    }

    private function getRequestPayload(): array
    {
        $parsed = $this->getPsrRequest()->getParsedBody();
        if (is_array($parsed) && $parsed !== []) {
            return $parsed;
        }

        $raw = (string) $this->getPsrRequest()->getBody();
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param mixed $value
     */
    private function parseBoolStrict(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) && ($value === 0 || $value === 1)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $normalizedValue = strtolower(trim($value));

            if (in_array($normalizedValue, ['1', 'true'], true)) {
                return true;
            }

            if (in_array($normalizedValue, ['0', 'false'], true)) {
                return false;
            }
        }

        return null;
    }

    private function isGetRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'GET';
    }

    private function isPostRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'POST';
    }

    private function isPutRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'PUT';
    }

    private function isDeleteRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'DELETE';
    }

    private function getItemCommentService(): ItemCommentService
    {
        return $this->getPsrContainer()->get(ItemCommentService::class);
    }
}
