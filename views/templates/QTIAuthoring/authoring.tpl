<script language="Javascript" type="text/javascript">
</script>

<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>util.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiEdit.js"></script>
<script type="text/javascript" src="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?=get_data('simplemodal_path')?>jquery.simplemodal.js"></script>

<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('jwysiwyg_path')?>jquery.wysiwyg.modal.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('simplemodal_path')?>jquery.simplemodal.css" type="text/css" />
<style type="text/css">
	div.wysiwyg ul.panel li a.html2 { background-position: -47px -46px;}
</style>

	
<div id='qtiAuthoring_itemEditor'>
    <textarea name="wysiwyg" id="wysiwyg" rows="10" cols="103"><?=get_data('itemData')?></textarea>
</div>
<label><input type="checkbox" value="1" id="click-inform" /> Inform about clicks.</label>

<div id='qtiAuthoring_interactionEditor'/>    
        
<script type="text/javascript">
//init the item's jwysiwyg editor here:
var addInteraction = {
	visible : true,
	className: 'addInteraction',
	exec: function(){
		CL('inserting interaction...');
		//display modal window with the list of available type of interactions
		
		//callback when the interaction has been created:
		
		//get its id:
		var interaction_id = 'interaction_'+12;
		
		//insert into the item editor:
		this.insertHtml('&nbsp;<button id="'+interaction_id+'" title="interaction name" value="&middot;&middot;&middot;"/>&nbsp;');
		var interaction = qtiEdit.getUniqueEltInFrame('#'+interaction_id);
		CL(interaction);
		interaction.height(20);
		
		//add click listener
		interaction.click(function(){
			
			CL('interaction clicked!');
		});
		
		//go to the form:
		// qtiEdit.loadInteractionForm(interaction_id);
	},
	tooltip: 'add interaction'
};

var save = null;
var loadXmlQti = null;
var exportXmlQti = null;

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