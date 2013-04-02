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
 * Poly represents a polygon managed by the system.
 * A polygon hosts a list points. 
 * A point is represented on the system by a "Tuple Variable"
 * of two integers.
 *
 * @extends TAO_MATCHING.Shape
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package TAO_MATCHING
 * 
 * @constructor 
 * @param {array} object Array of points
 */
TAO_MATCHING.Poly = function(data) {
    // Call the parent constructor
    TAO_MATCHING.Shape.call (this, data.type);
    
    /**
     * List of points which form the polygon
     *
     * @access protected
     * @var array
     */
    this.points = [];
    
    // treat the data
    for (var i in data.points) {
        this.points.push (TAO_MATCHING.VariableFactory.create(data.points[i]));
    }
};

TAO_MATCHING.Poly.prototype = {
    /**
     * Check if the polygon contains a point
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var The point to find.
	 * The point is represented by a "Tuple Variable" of two integers.
     * @return boolean
     */
    contains : function (point) {
        var returnValue = null;

        var point_x = (point.getValue())[0].getValue();
        var point_y = (point.getValue())[1].getValue();

        // Algorithm from http://jsfromhell.com/math/is-point-in-poly
        for(var c = false, i = -1, l = this.points.length, j = l - 1; ++i < l; j = i) {
            var point_i_x = (this.points[i].getValue())[0].getValue();
            var point_i_y = (this.points[i].getValue())[1].getValue();
            var point_j_x = (this.points[j].getValue())[0].getValue();
            var point_j_y = (this.points[j].getValue())[1].getValue();

            ( ((point_i_y <= point_y) && (point_y < point_j_y))
                || ((point_j_y <= point_y ) && (point_y < point_i_y))
            )
            && (point_x < ( (point_j_x - point_i_x) * (point_y - point_i_y) / (point_j_y - point_i_y) + point_i_x))
            && (c = !c);
        }
        
        returnValue = c;
        return returnValue;
    }
};

// Extend the class with its parent properties
TAO_MATCHING.Poly.prototype = $.extend ({}, TAO_MATCHING.Shape.prototype, TAO_MATCHING.Poly.prototype);
