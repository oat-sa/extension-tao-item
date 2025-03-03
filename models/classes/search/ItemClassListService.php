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
use oat\generis\model\data\permission\PermissionInterface;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\session\SessionService;
use oat\tao\model\TaoOntology;

class ItemClassListService
{
    private const CLASS_LIST_LIMIT = 25;
    private Ontology $ontology;
    private PermissionInterface $permissionManager;
    private SessionService $sessionService;

    public function __construct(Ontology $ontology, PermissionInterface $permissionManager, $sessionService)
    {
        $this->ontology = $ontology;
        $this->permissionManager = $permissionManager;
        $this->sessionService = $sessionService;
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

        $this->skipNotAccessible($searchResult);

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

    private function getDynamicQueryParameters(int $page, array $basicQueryParameters): array
    {
        return array_merge(
            $basicQueryParameters,
            [
                'limit' => self::CLASS_LIST_LIMIT,
                'offset' => ($page - 1) * self::CLASS_LIST_LIMIT
            ]
        );
    }

    private function skipNotAccessible(array &$results): void
    {
        if (!count($this->permissionManager->getSupportedRights())) {
            // if DAC is not enabled
            return;
        }

        $uris = array_map(function (core_kernel_classes_Resource $a): string {
            return $a->getUri();
        }, $results);

        $permissions = $this->permissionManager->getPermissions($this->sessionService->getCurrentUser(), $uris);

        foreach ($results as $key => &$row) {
            $uri = $row->getUri();
            if (isset($permissions[$uri]) && !in_array(PermissionInterface::RIGHT_WRITE, $permissions[$uri])) {
                unset($results[$key]);
            }
        }
    }
}
