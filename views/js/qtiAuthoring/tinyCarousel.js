tinyCarousel.instances = [];
function tinyCarousel(container, content, next, prev, total){

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
	this.total = parseInt(total);
	this.container = container;
	this.content = content;
	this.nextButton = next;
	this.prevButton = prev;
	var self = this;
	$(this.prevButton).click(function(){
		self.prev();
	}).hide();
	
	$(this.nextButton).click(function(){
		self.next();
	}).hide();
	
	// this.options = defaultOptions;
	this.init = function(){
		this.contentWidth = $(this.content).width();
		this.containerWidth = $(this.container).width();
		if(this.contentWidth && this.containerWidth){
			this.stepWidth = this.contentWidth/this.total;
			// Math.round
			if(this.contentWidth > this.containerWidth){
				$(this.nextButton).show();
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
		console.log(this);
		var extraSteps = ($(this.content).width()-$(this.container).width())/this.stepWidth;
		console.log('remain', extraSteps);
		if(this.current >= this.total - extraSteps){
			$(this.nextButton).hide();
		}else{
			$(this.nextButton).show();
		}
		
		if(this.current <= 0){
			$(this.prevButton).hide();
		}else{
			$(this.prevButton).show();
		}
	}
	
	//'resize', self.update
	// 
	
	this.init();
	tinyCarousel.instances[this.container] = this;
}

tinyCarousel.prototype.update = function(){
	//the container size might change, but content not
	this.updateButtonVisibility();
}