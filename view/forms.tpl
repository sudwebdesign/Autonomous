<after selector="input[name][type!=checkbox][type!=hidden], select[name], textarea[name]">
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

<attrappend selector="input[name][type=checkbox]" append="<?=isset($formPosted)&&!$formPosted?post::get_checked('{{this:name}}','{{this:checked}}',true):''?>">

<attr selector="input[name][type!=checkbox][type!=hidden]" value="<?=isset($formPosted)&&!$formPosted?post::get_text('{{this:name}}','{{this:+value}}',true):''?>">

<write selector="textarea[name]">
	<?=isset($formPosted)&&!$formPosted?post::get_text('{{this:name}}','{{this:value}}',true):''?>
</write>
