<?php
use oat\tao\helpers\Template;
?>
  
<div class="main-container flex-container-main-form">
    <h2><?=get_data('formTitle')?></h2>
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>

<?php Template::inc('footer.tpl'); ?>
