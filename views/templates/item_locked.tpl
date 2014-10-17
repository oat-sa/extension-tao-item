<?php
use oat\tao\helpers\Template;
?>
<header class="section-header flex-container-full">
    <h2><?=__('Item Locked')?></h2>
</header>
<div class="flex-container-half">

    <div class="feedback feedback-warning">
		<h3><span class="icon-lock big"></span> <?=__('This item is currently being edited and has been locked')?></h3> 
		<div class="grid-row">
		    <span class="col-2"><?=__('Item Locked:')?></span>
            <span class="col-2"><?=get_data('label')?></span>
        </div>
		<div class="grid-row">
		    <span class="col-2"><?=__('Lock Owner:')?></span>
            <span class="col-2"><a href="mailto:<?=get_data('ownerMail')?>"><?=get_data('ownerLogin')?></a></span>
        </div>
		<div class="grid-row">
		    <span class="col-2"><?=__('Locking Date:')?></span>
            <span class="col-2"><?=get_data('epoch')?></span>
        </div>
		
        <?php if (get_data('isOwner')): ?>
		<p>
            <em><?=__('As the owner of this resource, you may release the lock')?></em>
            <button id="release" class="btn btn-warning"><?=__('ReleaseLock')?></button>
        </p>
		<?php else : ?>
		<p><em><?=__('Please contact the owner of the resource to unlock it')?></em></p>
		<?php endif;?>
	    
	</div>
    
</div>
<script type="text/javascript">
require(
['jquery', 'i18n', 'helpers', 'lock', 'layout/section', 'ui/feedback'], 
function($, __, helpers, Lock, sectionApi, feedback){

    var uri = <?=json_encode(get_data('itemUri'))?>;
    var dest = helpers._url('editItem', 'Items', 'taoItems', {uri : uri});

    var successCallBack = function successCallBack() {
        sectionApi.current().loadContentBlock(dest);
    };
    var errorBack = function errorBack(){
        feedback().error(__('Unable to release the lock'));
    };
    $("#release").click(function(e) {
        e.preventDefault();
        new Lock(uri).release(successCallBack, errorBack);
    });
});
</script>
<?php Template::inc('footer.tpl', 'tao'); ?>
