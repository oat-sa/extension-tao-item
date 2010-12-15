TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Poly = function(data) {
    // Call the parent constructor
    TAO_MATCHING.Shape.call (this, data.type);
    // set points
    this.points = [];
    for (var i in data.points) {
        this.points.push (TAO_MATCHING.VariableFactory.create(data.points[i]));
    }
}

TAO_MATCHING.Poly.prototype = {
    contains : function (point) {
        var returnValue = null;

        var point_x = (point.getValue())[0].getValue();
        var point_y = (point.getValue())[1].getValue();

        // Algorithm from 
        /*var i = 0, j = 0, c = false;
        for (i = 0, j = this.points.length-1; i < this.points.length; j = i++) {
            var point_i_x = (this.points[i].getValue())[0].getValue();
            var point_i_y = (this.points[i].getValue())[1].getValue();
            var point_j_x = (this.points[j].getValue())[0].getValue();
            var point_j_y = (this.points[j].getValue())[1].getValue();
            //console.log (i, j, point_i_x, point_i_y, point_j_x, point_j_y);
            
            if ( ((point_i_y>point_y) != (point_j_y>point_y)) &&
                (point_x < (point_j_x-point_i_x) * (point_y-point_i_y) / (point_j_y-point_i_y) + point_i_x) )
                c = !c;
        }*/

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