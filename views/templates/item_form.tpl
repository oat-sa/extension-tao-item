<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<header class="section-header flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
</header>
<div class="main-container flex-container-main-form">
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>
<div class="data-container-wrapper flex-container-remaining"></div>

<script>
requirejs.config({
    config: {
        'taoItems/controller/items/edit': {
            'isPreviewEnabled' : <?= json_encode(get_data('isPreviewEnabled')) ?>,
            'isAuthoringEnabled' : <?= json_encode(get_data('isAuthoringEnabled')) ?>
        }
    }
});
</script>
