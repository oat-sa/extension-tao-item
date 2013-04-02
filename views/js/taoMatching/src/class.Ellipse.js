/*  
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};
/**
 * @class
 * 
 * Ellipse represents ellipse managed by the tao matching api.
 * Ellipse is used to represent ellipse and circle.
 *
 * @abstract
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * @todo Check if the function setTypes is deprecated
 * 
 * @constructor 
 * @param {array} data Data used to construct the ellipse
 */
TAO_MATCHING.Ellipse = function(data){
    // Call the parent constructor
    TAO_MATCHING.Shape.call (this, data.type);
    /**
     * Center of the Ellipse
     *
     * @access protected
     * @type {TAO_MATCHING.Tuple}
     */
    this.center = TAO_MATCHING.VariableFactory.create(data.center);
    /**
     * Horizontal radius of the ellipse
     *
     * @access protected
     * @var Integer
     */
    this.hradius = data.hradius;
    /**
     * Vertical radius of the ellipse
     *
     * @access protected
     * @var Integer
     */
    this.vradius = data.vradius;
};

TAO_MATCHING.Ellipse.prototype = {
    /**
     * Check if the polygon contains a point
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param {TAO_MATCHING.Variable} point The point to find. The point is represented by a "Tuple Variable" of two integers.
     * @return boolean
     */
    contains : function (point) {
        var returnValue = false;
        var point_x = (point.getValue())[0].getValue();
        var point_y = (point.getValue())[1].getValue();
        var center_x = (this.center.getValue())[0].getValue();
        var center_y = (this.center.getValue())[1].getValue();
        var a = Math.pow(point_x-center_x,2)/Math.pow(this.hradius,2);
        var b = Math.pow(point_y-center_y,2)/Math.pow(this.vradius,2);
        returnValue = a+b <= 1;
        return returnValue;
    }
};

// Extend the class with its parent properties
TAO_MATCHING.Ellipse.prototype = $.extend ({}, TAO_MATCHING.Shape.prototype, TAO_MATCHING.Ellipse.prototype);
