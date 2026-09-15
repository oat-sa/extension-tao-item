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

use oat\oatbox\event\Event;
use oat\tao\model\TaskOrchestrator\CommentMentionEmailTemplatePayload;

class CommentMentionNotificationRequestedEvent implements Event
{
    private string $commentId;
    private string $recipientUserUri;
    private string $recipientLogin;
    private string $recipientEmail;
    private string $actorLogin;
    private CommentMentionEmailTemplatePayload $payload;

    public function __construct(
        string $commentId,
        string $recipientUserUri,
        string $recipientLogin,
        string $recipientEmail,
        CommentMentionEmailTemplatePayload $payload,
        string $actorLogin
    ) {
        $this->commentId = $commentId;
        $this->recipientUserUri = $recipientUserUri;
        $this->recipientLogin = $recipientLogin;
        $this->recipientEmail = $recipientEmail;
        $this->payload = $payload;
        $this->actorLogin = $actorLogin;
    }

    public function getName(): string
    {
        return __CLASS__;
    }

    public function getCommentId(): string
    {
        return $this->commentId;
    }

    public function getRecipientUserUri(): string
    {
        return $this->recipientUserUri;
    }

    public function getRecipientLogin(): string
    {
        return $this->recipientLogin;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function getActorLogin(): string
    {
        return $this->actorLogin;
    }

    public function getPayload(): CommentMentionEmailTemplatePayload
    {
        return $this->payload;
    }
}
