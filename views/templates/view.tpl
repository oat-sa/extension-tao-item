<?foreach(get_data('itemProperties') as $itemProperty):?>
<span><?=$itemProperty['name']?></span> : <span><?=$itemProperty['value']?></span><br />
<?endforeach?>