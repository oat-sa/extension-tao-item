<script type="text/javascript">
requirejs.config({
    config: {
        'taoItems/controller/items': {
            'action' : "<?=get_data('action')?>",
            'uri' : "<?=get_data('uri')?>",
            'classUri' : "<?=get_data('classUri')?>",
            'label' : "<?=get_data('label')?>",
        <?if(get_data('reload')):?>
            'reload' : "<?=get_data('reload')?>",
        <?endif?>
        <?if(has_data('message')):?>
            'message' : "<?=get_data('message')?>",
        <?endif?>
        <?if(has_data('isAuthoringEnabled')):?>
            'isAuthoringEnabled' : <?=get_data('isAuthoringEnabled') ? 'true' : 'false'?>,
        <?endif?>
        <?if(has_data('isPreviewEnabled')):?>
            'isPreviewEnabled' : <?=get_data('isPreviewEnabled') ? 'true' : 'false'?>,
        <?endif?>
            'authoringUrl' : "<?=get_data('authoringUrl')?>",
            'previewUrl' : "<?=get_data('previewUrl')?>"
        }
    }
});
</script>
