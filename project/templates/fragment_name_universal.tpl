<fieldset class="fragment"><legend>Название работы на языке публикации</legend>
<table class="form">

	<tr>
		<td><textarea onblur="check_publ_exist(this);" {$class_req_input} required='required'
			placeholder="пример: Қолданбалы есептерді шешуде параллельді программалауды қолдану"
			{$readonly} type='text' id='name' name='name' cols='102' rows='3'>{$object->name}</textarea></td>
	</tr>
</table>
<div id="papers_founded_block" style="display:none">
<span style="color:red; font-weight: bold;">
Внимание! В базе данных найдены работы которые по названию имеют сходство с публикацией, что вы вводите.
Внимательно просмотрите список, нет ли среди них уже такой.  

</span>
<div id="papers_founded">

</div>
<button onclick="hideBlock('papers_founded_block');return false;">Скрыть (такой не имеется в списке)</button>
</div>
</fieldset>


