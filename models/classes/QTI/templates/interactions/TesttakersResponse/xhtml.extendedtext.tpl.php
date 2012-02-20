<div class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">
<?if(!empty($prompt)):?>
	<p class="prompt"><?=$prompt?></p>
<?endif?>

<?=$data?>

	<div id="<?=$identifier?>">
<?php foreach($testtakerResponses as $ttr): ?>
		<p><?=nl2br($ttr)?></p>
<?php endforeach; ?>
	</div>
</div>