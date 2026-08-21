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

namespace oat\taoItems\model\Comment;

final class ItemComment
{
    private string $id;
    private string $resourceUri;
    private string $resourceType;
    private string $authorId;
    private string $authorLabel;
    private string $body;
    private string $createdAt;
    private bool $edited;
    private bool $resolved;

    public function __construct(
        string $id,
        string $resourceUri,
        string $resourceType,
        string $authorId,
        string $authorLabel,
        string $body,
        string $createdAt,
        bool $edited = false,
        bool $resolved = false
    ) {
        $this->id = $id;
        $this->resourceUri = $resourceUri;
        $this->resourceType = ResourceCommentType::assertValid($resourceType);
        $this->authorId = $authorId;
        $this->authorLabel = $authorLabel;
        $this->body = $body;
        $this->createdAt = $createdAt;
        $this->edited = $edited;
        $this->resolved = $resolved;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getResourceUri(): string
    {
        return $this->resourceUri;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
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

    public function isEdited(): bool
    {
        return $this->edited;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function withEditedBody(string $body): self
    {
        return new self(
            $this->id,
            $this->resourceUri,
            $this->resourceType,
            $this->authorId,
            $this->authorLabel,
            $body,
            $this->createdAt,
            true,
            $this->resolved
        );
    }

    public function withResolved(bool $resolved): self
    {
        return new self(
            $this->id,
            $this->resourceUri,
            $this->resourceType,
            $this->authorId,
            $this->authorLabel,
            $this->body,
            $this->createdAt,
            $this->edited,
            $resolved
        );
    }

    public function toArray(bool $editable = false): array
    {
        return [
            'id' => $this->id,
            'resourceUri' => $this->resourceUri,
            'resourceType' => $this->resourceType,
            'authorId' => $this->authorId,
            'authorLabel' => $this->authorLabel,
            'body' => $this->body,
            'createdAt' => $this->createdAt,
            'edited' => $this->edited,
            'resolved' => $this->resolved,
            'editable' => $editable,
        ];
    }
}
