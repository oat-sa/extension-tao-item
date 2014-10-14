<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
    <header class="section-header container-full">
        <h2><?=get_data('formTitle')?></h2>
    </header>
    <div class="main-container">
        <div class="form-content">
            <?=get_data('myForm')?>
        </div>
    </div>
    <div class="data-container-wrapper container-remaining"></div>

<?php Template::inc('footer.tpl'); ?>