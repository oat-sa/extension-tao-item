<?php
use oat\tao\helpers\Template;
?>

<?php if(get_data('isPreviewEnabled')) : ?>
<div id="item-properties-form-column" class="item-properties-column item-properties-column--form">
    <header id="item-properties-form-header" class="section-header">
        <h2><?=get_data('formTitle')?></h2>
        <?php if(has_data('updatedAt')) : ?>
            <p><?=__('Last updated on %2s', tao_helpers_Date::displayeDate(get_data('updatedAt')))?></p>
        <?php endif; ?>
    </header>
    <div class="main-container">
        <?php if(has_data('lockDate')) : ?>
            <div id="lock-box"
                data-id="<?= get_data('id') ?>"
                data-msg="<?= __('You checked out this item %2s ago', tao_helpers_Date::displayInterval(get_data('lockDate'), tao_helpers_Date::FORMAT_INTERVAL_SHORT)) ?>"></div>
        <?php endif; ?>
        <div class="form-content">
            <?=get_data('myForm')?>
        </div>
    </div>
</div>
<div id="item-properties-preview-column" class="item-properties-column item-properties-column--preview">
    <header id="item-properties-preview-header" class="section-header item-properties-preview-header">
        <h2><?=__('Preview')?></h2>
    </header>
    <div class="data-container-wrapper">
        <div id="item-properties-preview" class="item-properties-preview"></div>
    </div>
</div>
<?php else : ?>
<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
    <?php if(has_data('updatedAt')) : ?>
        <p><?=__('Last updated on %2s', tao_helpers_Date::displayeDate(get_data('updatedAt')))?></p>
    <?php endif; ?>
</header>
<div class="main-container flex-container-main-form">
    <?php if(has_data('lockDate')) : ?>
        <div id="lock-box"
            data-id="<?= get_data('id') ?>"
            data-msg="<?= __('You checked out this item %2s ago', tao_helpers_Date::displayInterval(get_data('lockDate'), tao_helpers_Date::FORMAT_INTERVAL_SHORT)) ?>"></div>
    <?php endif; ?>
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>
<?php endif; ?>

<script>
requirejs.config({
    config: {
        'taoItems/controller/items/edit': {
            'isPreviewEnabled' : <?= json_encode(get_data('isPreviewEnabled')) ?>,
            'isAuthoringEnabled' : <?= json_encode(get_data('isAuthoringEnabled')) ?>,
            'itemUri' : <?= json_encode(get_data('itemUri')) ?>
        }
    }
});
</script>

<?php Template::inc('footer.tpl', 'tao'); ?>
