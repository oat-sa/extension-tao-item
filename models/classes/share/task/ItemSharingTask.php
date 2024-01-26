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

namespace oat\taoItems\model\share\task;

use Exception;
use InvalidArgumentException;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\reporting\Report;
use oat\taoItems\model\share\ItemSharingService;
class ItemSharingTask extends AbstractAction
{
    public function __invoke($params)
    {
        $report = new Report(Report::TYPE_INFO, __('Resource(s) sharing started'));

        try {
            $report->add(
                $this->getServiceManager()
                    ->getContainer()
                    ->get(ItemSharingService::class)
                    ->shareItems($params)
            );
            $report->setMessage(__('Resource(s) sharing finished'));
        } catch (InvalidArgumentException $exception) {
            $report->add(
                $report::createError($exception->getMessage(), $exception)
            );
            $this->getLogger()->error($exception->getMessage());
        } catch (Exception $exception) {
            $report->add(
                $report::createError($exception->getMessage(), $exception)
            );
            $this->getLogger()->error($exception->getMessage());
        }

        return $report;
    }
}
