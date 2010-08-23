<script language="Javascript" type="text/javascript">
CL = function(arg1, arg2){
	if(arg1){
		if(arg2){
			console.log(arg1, arg2);
		}else{
			console.log(arg1);
		}
	}
}
CD = function(object, desc){
	if(desc){
		console.log(desc+':');
	}
	console.dir(object);
}
getEltInFrame = function(selector){
	var foundElts = [];
	//for each iframe:
	$('iframe').each(function(){
	
		//get its document
		$(this).each( function(){
			var selectedDocument = this.contentWindow.document;
			$(selector, selectedDocument).each(function(){
				foundElts.push(this);  
			});
		});
	});
	return foundElts;
}
getUniqueEltInFrame = function(){

}

</script>
<script type="text/javascript" src="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?=get_data('simplemodal_path')?>jquery.simplemodal.js"></script>

<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.modal.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('simplemodal_path')?>jquery.simplemodal.css" type="text/css" />
<style type="text/css">
	div.wysiwyg ul.panel li a.html2 { background-position: -47px -46px;}
</style>

	
<div>
    <textarea name="wysiwyg" id="wysiwyg" rows="5" cols="103"></textarea>
    </div>
    <label><input type="checkbox" value="1" id="click-inform" /> Inform about clicks.</label>
    
        
<script type="text/javascript">
var addInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		CL('inserting interaction...');
		
		var interaction_id = 'interaction_'+12;
		this.insertHtml('&nbsp;<button id="'+interaction_id+'" title="interaction name" value="&middot;&middot;&middot;"/>&nbsp;');
		var interactions = getEltInFrame('#'+interaction_id);
		if(interactions.length != 1){
			throw 'incorrect number of interaction with the id '+interaction_id+' ('+interactions.length+')';
		}
		var interaction = $(interactions[0]);
		CL(interaction);
		interaction.height(20);
	},
	tooltip: 'add interaction'
};

(function($)
{
  $('#wysiwyg').wysiwyg({
    controls: {
      strikeThrough : { visible : true },
      underline     : { visible : true },
      
      justifyLeft   : { visible : true },
      justifyCenter : { visible : true },
      justifyRight  : { visible : true },
      justifyFull   : { visible : true },
      
      indent  : { visible : true },
      outdent : { visible : true },
      
      subscript   : { visible : true },
      superscript : { visible : true },
      
      undo : { visible : true },
      redo : { visible : true },
      
      insertOrderedList    : { visible : true },
      insertUnorderedList  : { visible : true },
      insertHorizontalRule : { visible : true },

      h4: {
              visible: true,
              className: 'h4',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h4>' : 'h4',
              tags: ['h4'],
              tooltip: 'Header 4'
      },
      h5: {
              visible: true,
              className: 'h5',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h5>' : 'h5',
              tags: ['h5'],
              tooltip: 'Header 5'
      },
      h6: {
              visible: true,
              className: 'h6',
              command: ($.browser.msie || $.browser.safari) ? 'formatBlock' : 'heading',
              arguments: ($.browser.msie || $.browser.safari) ? '<h6>' : 'h6',
              tags: ['h6'],
              tooltip: 'Header 6'
      },
      
      cut   : { visible : true },
      copy  : { visible : true },
      paste : { visible : true },
      html  : { visible: true },
      exam_html: { exec: function() { this.insertHtml('<abbr title="exam">Jam</abbr>') }, visible: true  },
	  addInteraction: addInteraction
    },
    events: {
      click : function(e)
      {
        if ($('#click-inform:checked').length > 0)
        {
          e.preventDefault();
          alert('You have clicked jWysiwyg content!');
        }
      }
    }
  });

  $('#wysiwyg').wysiwyg('insertHtml', 'sample code');

})(jQuery);
</script>