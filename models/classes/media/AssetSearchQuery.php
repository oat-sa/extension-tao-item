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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\media;

use oat\tao\model\media\MediaAsset;
use oat\tao\model\media\mediaSource\DirectorySearchQuery;

final class AssetSearchQuery extends DirectorySearchQuery
{
    public const SORT_LABEL = 'label';
    public const SORT_LOCATION = 'location';
    public const SORT_UPDATED_AT = 'updatedAt';

    public const DEFAULT_PAGE = 1;
    public const DEFAULT_PAGE_SIZE = 10;
    public const MAX_PAGE_SIZE = 100;

    /** @var string */
    private $query = '';

    /** @var string */
    private $sortBy = self::SORT_LABEL;

    /** @var string */
    private $sortDir = 'asc';

    /** @var int */
    private $page = self::DEFAULT_PAGE;

    /** @var int */
    private $pageSize = self::DEFAULT_PAGE_SIZE;

    /** @var int */
    private $depth;

    /** @var int */
    private $childrenLimit;

    public function __construct(
        MediaAsset $asset,
        string $itemUri,
        string $itemLang,
        array $filter = [],
        int $depth = 1,
        int $childrenOffset = 0,
        int $childrenLimit = 0
    ) {
        $normalizedDepth = $depth > 0 ? $depth : 1;
        parent::__construct($asset, $itemUri, $itemLang, $filter, $normalizedDepth, $childrenOffset, $childrenLimit);
        $this->depth = $normalizedDepth;
        $this->childrenLimit = $childrenLimit;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth > 0 ? $depth : 1;

        return $this;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function setChildrenLimit(int $childrenLimit): self
    {
        $this->childrenLimit = $childrenLimit;

        return $this;
    }

    public function getChildrenLimit(): int
    {
        return $this->childrenLimit;
    }

    public function setQuery(string $query): self
    {
        $this->query = trim($query);

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function hasQuery(): bool
    {
        return $this->query !== '';
    }

    public function setSortBy(string $sortBy): self
    {
        $allowed = [self::SORT_LABEL, self::SORT_LOCATION, self::SORT_UPDATED_AT];
        $this->sortBy = in_array($sortBy, $allowed, true) ? $sortBy : self::SORT_LABEL;

        return $this;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortDir(string $sortDir): self
    {
        $this->sortDir = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

        return $this;
    }

    public function getSortDir(): string
    {
        return $this->sortDir;
    }

    public function setPage(int $page): self
    {
        $this->page = $page > 0 ? $page : self::DEFAULT_PAGE;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPageSize(int $pageSize): self
    {
        if ($pageSize <= 0) {
            $this->pageSize = self::DEFAULT_PAGE_SIZE;
        } else {
            $this->pageSize = min($pageSize, self::MAX_PAGE_SIZE);
        }

        return $this;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}
