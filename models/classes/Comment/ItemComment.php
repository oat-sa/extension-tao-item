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
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\Comment;

final class ItemComment
{
    public const STATUS_ACTIVE = 'active';

    private string $id;
    private string $itemUri;
    private string $authorId;
    private string $authorLabel;
    private string $body;
    private string $createdAt;
    private string $status;

    public function __construct(
        string $id,
        string $itemUri,
        string $authorId,
        string $authorLabel,
        string $body,
        string $createdAt,
        string $status = self::STATUS_ACTIVE
    ) {
        $this->id = $id;
        $this->itemUri = $itemUri;
        $this->authorId = $authorId;
        $this->authorLabel = $authorLabel;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->status = $status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getItemUri(): string
    {
        return $this->itemUri;
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function getAuthorLabel(): string
    {
        return $this->authorLabel;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'itemUri' => $this->itemUri,
            'authorId' => $this->authorId,
            'authorLabel' => $this->authorLabel,
            'body' => $this->body,
            'createdAt' => $this->createdAt,
            'status' => $this->status,
        ];
    }
}
