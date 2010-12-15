TAO_MATCHING = typeof TAO_MATCHING != 'undefined' ? TAO_MATCHING : {};

TAO_MATCHING.Ellipse = function(data){
    // Call the parent constructor
    TAO_MATCHING.Shape.call (this, data.type);
    // set center
    this.center = TAO_MATCHING.VariableFactory.create(data.center);
    // set horizontal radius
    this.hradius = data.hradius;
    // set vertical radius
    this.vradius = data.vradius;
}

TAO_MATCHING.Ellipse.prototype = {
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
