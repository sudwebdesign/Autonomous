<!DOCTYPE html>
  <head>
    <title>Arborescence - SurikatBackOffice</title>
	<script type="text/javascript" src="/jquery+surikat.js"></script>
	<script type="text/javascript" src="/jquery/surikat-widgets.js"></script>
	<link rel="stylesheet" type="text/css" href="./arborescence.css" />
</head>
<body>
<div class="arbres_window_tohandle">
<div class="treewindow_path" data-path="{$smarty.get.path}">
	{assign var='segment_url' value=""}
	{foreach $dynamic.path_segments as $segment}
		{if $segment_url!=''}
			{assign var='segment_url' value="$segment_url/"}
		{/if}
		{assign var='segment_url' value="$segment_url$segment"}
		<a href="./?path={$segment_url}">{if $segment==''}.{else}{$segment}{/if}/</a>
	{/foreach}
</div>
<form method="POST" action="./?path={$smarty.get.path}">
	<input type="hidden" name="{$dynamic.type}[{$dynamic.type}_id]" value="{$dynamic.item.id}">
	<input type="text" name="{$dynamic.type}[base]" value="" placeholder="Nom de l'élément">
	<input type="submit" value="ajouter">
</form>
<div class="treewindow_children">
<table>
{assign var="gtarget" value="./?path=`$smarty.get.path`"}
{if $dynamic.type}
	{foreach $dynamic.children as $row}
		{assign var="target" value="./?path=`$dynamic.type``$dynamic.pathdir``$row.base`"}
		<tr>
			<td>
				{$row.id}
			</td>
			<td>
				<a href="{$target}">{$row.base}</a>
			</td>
			<td>
				<form method="POST" action="{$gtarget}">
					<input type="hidden" name="{$dynamic.type}[id]" value="{$row.id}">
					<input type="text" name="{$dynamic.type}[base]" value="{$row.base}">
					<input type="submit" value="rename">
				</form>
			</td>
			<td>
				<form method="POST" action="{$gtarget}">
					<input type="hidden" name="{$dynamic.type}[id]" value="{$row.id}">
					<input type="hidden" name="{$dynamic.type}[0]" value="delete">
					<input type="submit" value="remove">
				</form>
			</td>
			<td>
				<form method="POST" action="{$gtarget}">
					<input type="hidden" name="{$dynamic.type}[id]" value="{$row.id}">
					<input type="hidden" name="{$dynamic.type}[0]" value="position_up">
					<input type="submit" value="up">
				</form>
			</td>
			<td>
				<form method="POST" action="{$gtarget}">
					<input type="hidden" name="{$dynamic.type}[id]" value="{$row.id}">
					<input type="hidden" name="{$dynamic.type}[0]" value="position_down">
					<input type="submit" value="down">
				</form>
			</td>
		</tr>
	{/foreach}
	
	
{else}
	{foreach $dynamic.children as $type}
		<tr>
			<td>
				<a href="?path={$type}">{$type}</a>
			</td>
			<td>
				<form method="POST" action="{$gtarget}">
					<input type="hidden" name="action" value="remove">
					<input type="hidden" name="base" value="{$type}">
					<input type="submit" value="remove">
				</form>
			</td>
		</tr>
	{/foreach}
{/if}
</table>
<br><hr><br>
{if $dynamic.type}
	{input_post $dynamic.type posted}
	<form method="POST" data-posted="{$posted}" enctype="multipart/form-data" action={$gtarget} class="update-arbo-el" id="update_{$dynamic.type}_{$dynamic.item.id}">
		{foreach $dynamic.item as $k=>$v}
			{if !is_array($v)}
				{input model=$dynamic.type name=$k value=$v}<br>
			{/if}
		{/foreach}
		{input type="submit" value="Enregistrer"}
	</form>
{/if}
</div>
</div>
<script type="text/javascript">$.on('ready&/jquery/surikat-widgets.js',function(){
	$('form[action].update-arbo-el').formAutoWidgets();
});</script>
</body>
</html>