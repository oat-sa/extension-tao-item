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
 * Item comments REST API (FR1 M1).
 *
 * Routes:
 * - GET  /taoItems/RestItemComments/index?itemUri=
 * - POST /taoItems/RestItemComments/index  (itemUri, body)
 * - GET  /taoItems/RestItemComments/count?itemUri=
 */
class taoItems_actions_RestItemComments extends tao_actions_CommonModule
{
    use HttpJsonResponseTrait;

    public function index(): void
    {
        try {
            if ($this->isGetRequest()) {
                $itemUri = (string) ($this->getPsrRequest()->getQueryParams()['itemUri'] ?? '');
                $this->setSuccessJsonResponse($this->getItemCommentService()->list($itemUri));

                return;
            }

            if ($this->isPostRequest()) {
                $payload = $this->getRequestPayload();
                $itemUri = (string) ($payload['itemUri'] ?? '');
                $body = (string) ($payload['body'] ?? '');
                $comment = $this->getItemCommentService()->create($itemUri, $body);
                $this->setSuccessJsonResponse($comment->toArray(), 201);

                return;
            }

            $this->setErrorJsonResponse('Method not allowed', 405, [], 405);
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to process item comments request', 500, [], 500);
        }
    }

    public function count(): void
    {
        try {
            if (!$this->isGetRequest()) {
                $this->setErrorJsonResponse('Method not allowed', 405, [], 405);

                return;
            }

            $itemUri = (string) ($this->getPsrRequest()->getQueryParams()['itemUri'] ?? '');
            $this->setSuccessJsonResponse([
                'count' => $this->getItemCommentService()->count($itemUri),
            ]);
        } catch (common_exception_Unauthorized $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 403, [], 403);
        } catch (InvalidArgumentException $exception) {
            $this->setErrorJsonResponse($exception->getMessage(), 412, [], 412);
        } catch (Throwable $exception) {
            $this->logError($exception->getMessage());
            $this->setErrorJsonResponse('Unable to process item comments count', 500, [], 500);
        }
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

    private function isGetRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'GET';
    }

    private function isPostRequest(): bool
    {
        return strtoupper($this->getPsrRequest()->getMethod()) === 'POST';
    }

    private function getItemCommentService(): ItemCommentService
    {
        return $this->getPsrContainer()->get(ItemCommentService::class);
    }
}
