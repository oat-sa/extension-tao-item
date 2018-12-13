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
 * Copyright (c) 2017-2018 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Rest interface to manage forms to create and edit items
 *
 */
class taoItems_actions_RestFormItem extends \tao_actions_RestResource
{

    /**
     * Create only authorize for GET requests by now
     * @throws \common_exception_Unauthorized
     */
    public function create()
    {
        if ($this->isRequestGet()) {
            return parent::create();
        }
        else {
            throw new \common_exception_Unauthorized();
        }
    }
    /**
     * Edition is disabled now
     * @throws \common_exception_Unauthorized
     */
    public function edit()
    {
        throw new \common_exception_Unauthorized();
    }

    /**
     * Return the form object to manage user edition or creation
     *
     * @param $instance
     * @return taoItems_actions_form_RestItemForm
     */
    protected function getForm($instance)
    {
        return $this->propagate(new \taoItems_actions_form_RestItemForm($instance));
    }
}
