<?if(get_data('grid')):?>
	
<table id="grid"></table>
<div id="grid-pager"></div>

<script type="text/javascript">
	
	$(function(){
		$("#grid").jqGrid({
			url:"<?=get_data('dataUrl')?>",
			datatype: 'json',
			mtype: 'POST',
		    colNames:['<?=__("Index")?>','<?=__("Date")?>', '<?=__("User")?>', '<?=__("Comment")?>'],
		    colModel :[ 
		      {name:'id', index:'rid', width:75}, 
		      {name:'date', index:'date'}, 
			  {name:'user', index:'user'}, 
		      {name:'comment', index:'comment', sortable:false} 
		    ],
		    pager: '#grid-pager',
		    rowNum: 5,
		 /*   rowList:[10,20,30],*/
		    sortname: 'invid',
		    sortorder: 'desc',
		    viewrecords: true,
			height: '80%',
		    caption: '<?=__("History")?>'
		});
	});
	
</script>
<?endif?>