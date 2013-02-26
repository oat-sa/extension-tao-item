define(['require', 'jquery', root_url  + 'taoItems/views/js/qtiAuthoring/class.Data.js'], function(req, $, QTIdataClass){
	
	var QTIitemClassFunctions = {
		init : function(serial, options){
			this._super('item', serial, options);
		}
	}
	
	var QTIitemClass = QTIdataClass.extend(QTIitemClassFunctions);

	return QTIitemClass;
	
});

