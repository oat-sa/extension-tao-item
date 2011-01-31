tinyCarousel.instances = [];
function tinyCarousel(container, content, next, prev, options){

	// var defaultOptions = {
		// total:<?=$total?>,
		// current:0,
		// containerWidth:0,
		// contentWidth:0,
		// stepWidth:0,
		// container: '#container',
		// content:'#content'
	// };
	
	this.current = 0;
	this.options = options;
	// this.total = parseInt(total);
	this.container = container;
	this.content = content;
	
	//count the number of elements in container:
	var total = null;
	if(!total){
		this.total = $(this.content).children('div').length;
		if(this.total){
			var $refElt = $($(this.content).children('div')[0]);
			this.stepWidth = parseInt($refElt.width()); 
			// this.stepWidth += parseInt($refElt.css('border-left')); 
			// this.stepWidth += parseInt($refElt.css('border-right')); 
			this.stepWidth += parseInt($refElt.css('padding-left'));
			this.stepWidth += parseInt($refElt.css('padding-right'));
			// this.stepWidth += parseInt($refElt.css('margin-left'));
			// this.stepWidth += parseInt($refElt.css('margin-right'));
				
			this.contentWidth = this.total * this.stepWidth;
			
			$(this.content).width(this.contentWidth);
			$(this.container).css('overflow', 'hidden');
		}
	}else{
		this.total = total;
	}
	
	
	
	this.nextButton = next;
	this.prevButton = prev;
	var self = this;
	$(this.prevButton).click(function(){
		self.prev();
	}).css('visibility', 'hidden');
	
	$(this.nextButton).click(function(){
		self.next();
	}).css('visibility', 'hidden');
	
	// this.options = defaultOptions;
	this.init = function(){
		this.contentWidth = $(this.content).width();
		this.containerWidth = $(this.container).width();
		if(this.contentWidth && this.containerWidth){
		
			if(!this.stepWidth) this.stepWidth = this.contentWidth/this.total;
			
			// Math.round
			if(this.contentWidth > this.containerWidth){
				$(this.nextButton).css('visibility', 'visible');
			}
			
		}
	}	
	
	this.prev = function(){
		if(this.current>0){
			this.current--;
			this.updateButtonVisibility();
			
			if(this.stepWidth){
				var newMarginLeft = parseInt($(this.content).css('margin-left'))+this.stepWidth;
				$(this.content).css('margin-left', parseInt(newMarginLeft)+'px');
			}
			
		}
	}
	
	this.next = function(){
		if(this.current<this.total){
			this.current++;
			this.updateButtonVisibility();
			
			if(this.stepWidth){
				var newMarginLeft = parseInt($(this.content).css('margin-left'))-this.stepWidth;
				$(this.content).css('margin-left', parseInt(newMarginLeft)+'px');
			}
			
		}
	}
	
	this.updateButtonVisibility = function(){
		
		var extraSteps = ($(this.content).width()-$(this.container).width())/this.stepWidth;
				
		if(this.current >= this.total - extraSteps){
			$(this.nextButton).css('visibility', 'hidden');
		}else{
			$(this.nextButton).css('visibility', 'visible');
		}
		
		if(this.current <= 0){
			$(this.prevButton).css('visibility', 'hidden');
		}else{
			$(this.prevButton).css('visibility', 'visible');
		}
	}
	
	this.init();
	tinyCarousel.instances[this.container] = this;
}

tinyCarousel.prototype.update = function(){
	//the container size might change, but content not
	this.updateButtonVisibility();
}