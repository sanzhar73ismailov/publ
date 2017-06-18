<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css">
<title>{$title}</title>
{include file="js_include.tpl"} {if isset($generate_script) }
{$generate_script} {/if}
<script src="jscript/publForm.js"></script>

</head>
<body>


<div id="wrap">{include file="header.tpl"}



<div id="content">
<div class="center_title">{$title}</div>

{if isset($message) }

<h2>
<div style="color: green;">{$message}</div>
</h2>

{/if}

<form method="post" action="index.php" onsubmit="return checkform(this)">
<input type="hidden" name="do" value="save" />
<input type="hidden" name="page" value="{$entity}" /> 
<input type="hidden" name="entity" value="{$entity}" />
<input type="hidden" name="type_id" value="{$object->type_id}" />
<input type="hidden" name="type_publ" value="{$type_publ}" />
	
	

<table class="form">
	{if $edit}
	<tr>
		<td><input type="submit" value="Сохранить"
			style="width: 120px; height: 30px"></td>
		<td class="req_input">Обязательные поля выделены этим цветом, <br />
		без их заполнения данные не сохранятся!</td>
	</tr>
	{else} {if isset($can_edit)} {if $can_edit==1}
	<tr>
		<td><a href="index.php?page={$entity}&id={$object->id}&do=edit&type_publ={$type_publ}"> <img
			width="20" height="20" alt="Править" src="images/edit.jpg" /></a></td>

	</tr>
	{/if} {else}
	<tr>
		<td><a href="index.php?page={$entity}&id={$object->id}&do=edit&type_publ={$type_publ}"> <img
			width="20" height="20" alt="Править" src="images/edit.jpg" /></a></td>

	</tr>
	{/if} {/if}


	<tr>
		<td>Код публикации</td>
		<td>{if $object->id} {$object->id} <input type="hidden" name="id"
			value="{$object->id}" /> {else} <input type="hidden" name="id"
			value="0" />
		<div style="background-color: #d14836">Новый</div>
		{/if}</td>

	</tr>
	
	<tr>
		<td>Название</td>
		<td><textarea {$class_req_input} required='required'
			{$readonly} name='name' cols='62' rows='3'>{$object->name}</textarea>
	
	</tr>
	

	<tr>
		<td>Город</td>
		<td><input {$class_req_input} required='required' type="text"
			{$readonly} name="book_city" size="50"
			value="{$object->book_city}" /></td>
	</tr>

	<tr>
		<td>Количество страниц</td>
		<td><input {$class_req_input} required='required' type="number" min = "1"
			{$readonly} name="book_pages" size="50"
			value="{$object->book_pages}" /></td>
	</tr>
	
	<tr>
		<td>Издательство</td>
		<td><input  type="text"
			{$readonly} name="izdatelstvo" size="50"
			value="{$object->izdatelstvo}" /></td>
	</tr>
	
	
<tr>
		<td>Год издания</td>
		<td><input {$class_req_input} required='required' type="number" min = "1945"
			{$readonly} name="year" size="50"
			value="{$object->year}" /></td>
	</tr>

<tr>
		<td>Удк</td>
		<td><input {$class_req_input} required='required' type="text" 
			{$readonly} name="code_udk" size="50"
			value="{$object->code_udk}" /></td>
	</tr>

<tr>
		<td>ББК</td>
		<td><input {$class_req_input} required='required' type="text" 
			{$readonly} name="method_recom_bbk" size="50"
			value="{$object->method_recom_bbk}" /></td>
	</tr>

<tr>
		<td>ISSN</td>
		<td><input type="text" 
			{$readonly} name="journal_issn" size="50"
			value="{$object->journal_issn}" /></td>
	</tr>

<tr>
		<td>ISBN</td>
		<td><input type="text" 
			{$readonly} name="isbn" size="50"
			value="{$object->isbn}" /></td>
	</tr>

<tr>
		<td>Под редакцией</td>
		<td><input type="text" 
			{$readonly} name="method_recom_edited" size="50"
			value="{$object->method_recom_edited}" /></td>
	</tr>

<tr>
		<td>Утверждены ...(например, утверждены на Ученом Совете Казахского НИИ онкологии и радиологии, протокол №14 от 6 декабря 2012г.)</td>
			<td><textarea {$class} 
			{$readonly} name='method_recom_stated' cols='62' rows='3'>{$object->method_recom_stated}</textarea>
			</td>
	</tr>
	
<tr>
		<td>Одобрены ...(например,одобрены Экспертным Советом по вопросам развития здравоохранения МЗ РК, протокол №21 от 14 декабря 2012 года.)</td>
			<td><textarea {$class} 
			{$readonly} name='method_recom_approved' cols='62' rows='3'>{$object->method_recom_approved}</textarea>
			</td>
	</tr>

<tr>
		<td>Издано при поддержке ...(например, распечатано при поддержке ТОО 'Юмгискор Холдинг' или изданы при поддержке Представительства АО 'Берлин-Хеми АГ' в РК)</td>
			<td><textarea {$class} 
			{$readonly} name='method_recom_published_with_the_support' cols='62' rows='3'>{$object->method_recom_published_with_the_support}</textarea>
			</td>
	</tr>
	
	<tr>
		<td>Краткая аннотация и для кого предназначено</td>
			<td><textarea {$class_req_input} required='required'
			{$readonly} name='abstract_original' cols='62' rows='3'>{$object->abstract_original}</textarea>
			</td>
	</tr>
	
	<tr>
		<td>Рецензенты</td>
		<td><textarea {$class_req_input} required='required'
			{$readonly} name='method_recom_reviewers' cols='62' rows='3'>{$object->method_recom_reviewers}</textarea>
			</td>
	
<tr>
		<td>Количество таблиц</td>
		<td><input {$class_req_input} required='required' type="number" min = "0"
			{$readonly} name="number_tables" size="50"
			value="{$object->number_tables}" /></td>
	</tr>
	
	<tr>
		<td>Количество иллюстраций</td>
		<td><input {$class_req_input} required='required' type="number" min = "0"
			{$readonly} name="number_ilustrations" size="50"
			value="{$object->number_ilustrations}" /></td>
	</tr>

</table>

<h3>Автор(ы) публикации</h3>

<table class="numerated-table" id="authors">
	<tr>
		<th>N</th>
		<th>Это я</th>
		<th>Фамилия (полностью)</th>
		<th>Имя<br />
		(инициал, без точки)</th>
		<th>Отчество<br />
		(инициал, без точки)</th>
		<th>8. Место работы автора(ов) <br />
		(полное название организации)<br />
		например: Национальная инженерная академия <br />
		или КазНУ им. аль-Фараби</th>
		<th><a onclick="addItem('last_author'); return false;">
		<button>Добавить автора</button>
		</a></th>
	</tr>
	<tbody>


		{foreach $object->authors_array as $item}
		<tr {if $item@last}id='last_author'{/if}>
			<td><input type='hidden' name='c07_authors_id[]' value='{$item->id}' /></td>
			
			<td><input class="my_publ" id="c07_authors_me1{{"1"|mt_rand:1000000}}" type="checkbox"
				name='c07_authors_me[]' 
				onclick="checkOnlyOneSelected(this);" /></td>
	
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_lastname[]'
				value='{$item->last_name}' size='30' /></td>
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_firstname[]'
				value='{$item->first_name}' size='2' maxlength="1" /></td>
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_patrname[]'
				value='{$item->patronymic_name}' size='2' maxlength="1" /></td>
				
			<td><textarea {$class_req_input} required='required' {$readonly} name='c08_place_working_authors[]' cols='45' rows='3'>{$item->organization_name}</textarea></td>
			
			
			<td>{if $item@first}-{else}<a onclick="delFunct(this);">
			<button>Удалить</button>
			</a>{/if}</td>
		</tr>


		{foreachelse}
		<tr id='last_author'>
			<td></td>
			
			<td><input class="my_publ" id="c07_authors_me1" type="checkbox"
				name='c07_authors_me[]' value='0'
				onclick="checkOnlyOneSelected(this);" /></td>
			
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_lastname[]' value=''
				size='30' /></td>
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_firstname[]' value=''
				size='2' maxlength="1" /></td>
			<td><input {$class_req_input} required='required' type='text' {$readonly} name='c07_authors_patrname[]' value=''
				size='2' maxlength="1" /></td>
			<td><textarea {$class_req_input} required='required' {$readonly} name='c08_place_working_authors[]' cols='45' rows='3'>Казахский научно-исследовательский институт онкологии и радиологии МЗ РК</textarea></td>
			<td>-</td>
		</tr>
		{/foreach}


	</tbody>
</table>
<table class = "form">
	{if $edit}
	<tr>
		<td><input type="submit" value="Сохранить"
			style="width: 120px; height: 20px"></td>
		<td><input type="reset" value="Сброс"
			style="width: 120px; height: 20px"></td>
	</tr>
	{else} {/if}

</table>

</form>


</div>

{include file="footer.tpl"}</div>

</body>
</html>
