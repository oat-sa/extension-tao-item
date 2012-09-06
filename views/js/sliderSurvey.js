$(function () {
	createSliders('ltr'); //@TODO get the mode dynamically
})

function createSliders(mode) {
	if(typeof(mode) == 'undefined') {
		mode = 'ltr';
	}
	var ltr = (mode == 'ltr');
	var range = ltr ? 'min': 'max';
	$.each($('.slider'), function () {
// 		console.log(this);
		// get the params in numeric format
		var step = $(this).find('.step').val() * 1;
		var min = $(this).find('.min').val() * 1;
		var max = $(this).find('.max').val() * 1;
//    step = 1;
//    min = 0;
//    max = 5;
		// transform the div to slider
		$(this).slider({
			range: range,
			value:ltr ? min: max,
			min: min,
			max: max,
			step: step,
			change: function( event, ui ) {
				var val = ui.value;
				if(!ltr) {
					val = max - val + min;
				}
				$("[id^='value_" + $(this).attr('id') + "']")[0].value = val;
			}
		});
    // to avoid freeze if not filled attribute
    if(step != 0 && min < max) {
      // generrate vertical graduation bars
      for (var i = min; i <= max; i += step) {
        var left = ((i - min) * $(this).width()) / (max - min);
        var graduation = $('<div />').css({
          'position': 'absolute',
          'top': '18px',
          'left': left + 'px',
          'width': '1px',
          'height': '10px',
          'backgroundColor': 'black'
        });
        $(graduation).appendTo($(this));
      }
      // generate horizontal graduation bar
      var graduationBar = $('<div />').css({
        'position': 'absolute',
        'top': '23px',
        'left': '0px',
        'width': $(this).width() + 'px',
        'height': '1px',
        'backgroundColor': 'black'
      });
      $(graduationBar).appendTo($(this));
    }
	});
}
// put a label on each side ok the slider
function labels (min, max) {
	if(typeof(min) == 'undefined') {
		if(typeof(js_translation['slider_level_min']) == 'undefined') {
			min = 'Low';
		} else {
			min = js_translation['slider_level_min'];
		}
	}
	if(typeof(max) == 'undefined') {
		if(typeof(js_translation['slider_level_max']) == 'undefined') {
			max = 'High';
		} else {
			max = js_translation['slider_level_max'];
		}
	}
	// set text level min and max for slider
	$('.levelMin').html(min);
	$('.levelMax').html(max);
}