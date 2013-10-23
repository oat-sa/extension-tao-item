
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/layout.css" />
<div class="main-container">
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
<?=__('Item Locked')?>
</div>
    <center>
	<span class="tileBox">
	    <span class="header">
		<?=__('This item is currently being edited and has been locked')?>
	    </span>
	    <table>
		<tr><td rowspan="3"><img src="<?=TAOBASE_WWW?>/img/lock.png" /> </td><td class="fieldLabel"><?=__('Item Locked:')?></td><td class="fieldValue"><?=get_data('label')?></td></tr>
		<tr><td class="fieldLabel"><?=__('Lock Owner:')?></td><td class="fieldValue"><?=get_data('owner')?></td></tr>
		<tr><td class="fieldLabel"><?=__('Locking Date:')?></td><td class="fieldValue"><?=get_data('epoch')?></td></tr>
		<tr />
	    </table>
	    <span class="button" id="release">
		<?=__('ReleaseLock')?>
	    </span>
	</span>
    </center>
</div>
<script src="<?= ROOT_URL ?>tao/views/js/lock.js" />
<script>
    var lock = new Lock('<?=get_data('uri')?>');
    $("#release").click(function() {
	lock.release();
    }
    );
    
</script>
<?if(!get_data('isDeprecated')):?>
	<? include('footer.tpl') ?>
<?endif?>