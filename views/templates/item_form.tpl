<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>



<div class="main-container">
	<h2><?=get_data('formTitle')?></h2>
	<div class="form-content">
		<?=get_data('myForm')?>
	</div>
</div>
