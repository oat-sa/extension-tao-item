<?php
use oat\tao\helpers\Template;
?>

<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div id="lock-box"></div>
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>

<script>
requirejs.config({
    config: {
        'taoItems/controller/items/edit': {
            'msg' : <?= json_encode(
                has_data('lockDate')
                ? __('You checked out this %1s %2s ago', 'item', tao_helpers_Date::displayInterval(get_data('lockDate'), tao_helpers_Date::FORMAT_INTERVAL_SHORT))
                : false) ?>,
            'uri' : <?= json_encode(get_data('id')) ?>,
            'isPreviewEnabled' : <?= json_encode(get_data('isPreviewEnabled')) ?>,
            'isAuthoringEnabled' : <?= json_encode(get_data('isAuthoringEnabled')) ?>
        }
    }
});
</script>

<?php Template::inc('footer.tpl', 'tao'); ?>
