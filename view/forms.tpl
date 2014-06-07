<after selector="input[name][type!=checkbox], select[name], textarea[name]">
	<?
		if(isset($formErrors)&&isset($formErrors["{{compile:
			rtrim(str_replace(array('[',']'),array('.',''),'{{this:name}}'),'.')
		}}"])){
			?><small class="help-block"><?=$formErrors["{{compile:
				rtrim(str_replace(array('[',']'),array('.',''),'{{this:name}}'),'.')
			}}"]?></small><?
		}
	?>
</after>
<attrappend selector="input[name][type=checkbox]" append="<?=post::get_checked('{{this:name}}','',true)?>">
<attr selector="input[name][type!=checkbox]" value="<?=post::get_text('{{this:name}}','{{this:checked}}',true)?>">
<write selector="textarea[name]">
	<?=post::get_text('{{this:name}}','{{this:value}}',true)?>
</write>
