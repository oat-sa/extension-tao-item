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

namespace oat\taoItems\model\event;

use JsonSerializable;
use oat\oatbox\event\Event;

class CommentMentionUsedEvent implements Event, JsonSerializable
{
    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';

    private string $commentId;
    private string $resourceUri;
    private string $resourceType;
    private string $mentionedBy;
    private string $mentionedUserId;
    private ?string $mentionedUserLogin;
    private string $action;

    public function __construct(
        string $commentId,
        string $resourceUri,
        string $resourceType,
        string $mentionedBy,
        string $mentionedUserId,
        ?string $mentionedUserLogin,
        string $action
    ) {
        $this->commentId = $commentId;
        $this->resourceUri = $resourceUri;
        $this->resourceType = $resourceType;
        $this->mentionedBy = $mentionedBy;
        $this->mentionedUserId = $mentionedUserId;
        $this->mentionedUserLogin = $mentionedUserLogin;
        $this->action = $action;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getCommentId(): string
    {
        return $this->commentId;
    }

    public function getResourceUri(): string
    {
        return $this->resourceUri;
    }

    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    public function getMentionedBy(): string
    {
        return $this->mentionedBy;
    }

    public function getMentionedUserId(): string
    {
        return $this->mentionedUserId;
    }

    public function getMentionedUserLogin(): ?string
    {
        return $this->mentionedUserLogin;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function jsonSerialize(): array
    {
        return [
            'commentId' => $this->commentId,
            'resourceUri' => $this->resourceUri,
            'resourceType' => $this->resourceType,
            'mentionedBy' => $this->mentionedBy,
            'mentionedUserId' => $this->mentionedUserId,
            'mentionedUserLogin' => $this->mentionedUserLogin,
            'action' => $this->action,
        ];
    }
}
