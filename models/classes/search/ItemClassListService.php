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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\search;

use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\OntologyRdfs;
use oat\tao\model\TaoOntology;

class ItemClassListService
{
    private const CLASS_LIST_LIMIT = 10;
    private ComplexSearchService $complexSearchService;
    private Ontology $ontology;
    public function __construct(ComplexSearchService $complexSearchService, Ontology $ontology)
    {
        $this->complexSearchService = $complexSearchService;
        $this->ontology = $ontology;
    }

    public function getList(string $query, string $page): array
    {
        $page = (int) $page;
        $root = $this->ontology->getClass(TaoOntology::CLASS_URI_ITEM);
        $basicQueryParameters = [
            'recursive' => true,
            'like' => true,
            'onlyClass' => true
        ];

        $query = [
            OntologyRdfs::RDFS_LABEL => $query
        ];

        $searchResult = $root->searchInstances(
            $query,
            $this->getDynamicQueryParameters($page, $basicQueryParameters)
        );

        $result['total'] = $root->countInstances($query, $basicQueryParameters) ?? 0;
        $result['items'] = [];

        foreach ($searchResult as $row) {
            $result['items'][] = [
                'id' => $row->getUri(),
                'uri' => $row->getUri(),
                'text' => $row->getLabel(),
                'path' => $this->getListElementText($row)
            ];
        }
        return $result;
    }

    private function getListElementText(core_kernel_classes_Resource $row): string
    {
        $displayText = '';
        foreach ($row->getParentClassesIds() as $parent) {
            if ($parent !== TaoOntology::CLASS_URI_ITEM) {
                $displayText .= $this->ontology->getResource($parent)->getLabel();
                $displayText .= '/';
            }
        }

        $displayText .= $row->getLabel();
        return $displayText;
    }

    private function getDynamicQueryParameters(int $page, array $basicQueryParameters)
    {
        return array_merge(
            $basicQueryParameters,
            [
                'limit' => self::CLASS_LIST_LIMIT,
                'offset' => ($page - 1) * self::CLASS_LIST_LIMIT
            ]
        );
    }
}
