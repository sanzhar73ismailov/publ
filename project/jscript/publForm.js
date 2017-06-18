function copyFromOtherInput(obj) {
	var fromInput = document.getElementById('abstract_original');
	var thisInput = document.getElementById(obj.id);
	var idCopyToEl = obj.id.replace("copy_to_", "");
	
	var copyToInput = document.getElementById(idCopyToEl);
	
	fromInput.value = fromInput.value.trim();

	if (fromInput.value == "") {
		alert("Поле пустое, вставляеть нечего!");
	} else {
		copyToInput.value = fromInput.value;
	}
	thisInput.checked = false;

}

function showFullSelectedLabe(obj){
	var thisSelectInput = document.getElementById(obj.id);
	var placeToInserText = document.getElementById(obj.id + '_text');
	var textSelected = thisSelectInput.options[thisSelectInput.selectedIndex].text;
	//placeToInserText.appendChild(document.createTextNode(textSelected));
	placeToInserText.innerHTML  = textSelected;
	//alert(thisSelectInput.options[thisSelectInput.selectedIndex].text);
	//placeToInserText.innerHtml = thisSelectInput.options[thisSelectInput.selectedIndex].text;
}

function showAddAjaxForm(obj) {

	var currentEl = document.getElementById(obj.id);
	var idJQueryPlace = "#" + obj.id + '_place';
	if (currentEl.checked == true) {
		var currentEl = document.getElementById(obj.id + '_place');
		$(idJQueryPlace).show();

	} else {
		$(idJQueryPlace).hide();
	}

}

function addByAjaxAndSelectItem(obj) {

	// example of obj.id add_journal_button

	var strId = obj.id;
	var object = strId.replace("add_", "").replace("_button", "");
	var idOfSelectElement = object + "_id";
	var idOfDivForPlace = strId.replace("_button", "") + "_place";
	var idOfCheckBoxForShow = strId.replace("_button", "");

	// console.log("idOfSelectElement",idOfSelectElement);
	// console.log("idOfDivForPlace",idOfDivForPlace);
	// console.log("idOfCheckBoxForShow",idOfCheckBoxForShow);

	if (object == 'journal') {
		var country = $("#j_country").val().trim();
		var issn = $("#j_issn").val().trim();
		var name = $("#j_name").val().trim();
		var periodicity = $("#j_periodicity").val().trim();
		var izdatelstvo_mesto_izdaniya = $("#j_izdatelstvo_mesto_izdaniya")
				.val().trim();

		if (country == '' || issn == '' || name == '' || periodicity == ''
				|| izdatelstvo_mesto_izdaniya == ''

		) {
			alert('Для добавления журнала все поля должны быть заполнены!');
			return;
		}

		if (periodicity.match(/^[0-9]+$/) == null) {
			alert('Ошибка, периодичность - цифровое значение!');
			return;
		}

		$.post("./ajax_add_item.php", {
			object : object,
			country : country,
			issn : issn,
			name : name,
			periodicity : periodicity,
			izdatelstvo_mesto_izdaniya : izdatelstvo_mesto_izdaniya
		}, function(data) {
			if (data.length > 0) {
				var json = jQuery.parseJSON(data);

			}
			var select = document.getElementById(idOfSelectElement);
			var optionElement = document.createElement("option");
			optionElement.setAttribute("value", json.id);

			optionElement.setAttribute("selected", "selected");
			optionElement.appendChild(document.createTextNode(json.name));
			select.appendChild(optionElement);
			sortSelect(select);

			$("#" + idOfDivForPlace).slideUp();
			document.getElementById(idOfCheckBoxForShow).checked = false;

		});
	} else if (object == 'conference') {

		var name = $("#conference_name").val().trim();
		var sbornik_name = $("#sbornik_name").val().trim();
		var city = $("#conference_сity").val().trim();
		var country = $("#conference_country").val().trim();
		var type_id = $("#conference_type_id").val().trim();
		var level_id = $("#conference_level_id").val().trim();
		var date_start = $("#conference_date_start").val().trim();
		var date_finish = $("#conference_date_finish").val().trim();
		var add_info = $("#add_info").val().trim();


		/*console.log("name:" + name);
		console.log("sbornik_name:" + sbornik_name);
		console.log("city:" + city);
		console.log("country:" + country);
		console.log("type_id:" + type_id);
		console.log("level_id:" + level_id);
		console.log("date_start:" + date_start);
		console.log("date_finish:" + date_finish);
		console.log("add_info:" + add_info);
		*/
		if (name == '' || sbornik_name == '' || city == '' || country == ''
				|| type_id == '' || level_id == '' || date_start == ''
				|| date_finish == ''

		) {
			alert('Для добавления конференции все поля должны быть заполнены!');
			return;
		}

		$.post("./ajax_add_item.php", {
			object : object,
			name : name,
			sbornik_name : sbornik_name,
			city : city,
			country : country,
			type_id : type_id,
			level_id : level_id,
			date_start : date_start,
			date_finish : date_finish,
			add_info : add_info
		}, function(data) {
			if (data.length > 0) {
				//alert(data);
				var json = jQuery.parseJSON(data);

			}
			var select = document.getElementById(idOfSelectElement);
			var optionElement = document.createElement("option");
			optionElement.setAttribute("value", json.id);

			optionElement.setAttribute("selected", "selected");
			optionElement.appendChild(document.createTextNode(json.name));
			select.appendChild(optionElement);
			sortSelect(select);

			$("#" + idOfDivForPlace).slideUp();
			document.getElementById(idOfCheckBoxForShow).checked = false;

		});
	}else if (object == 'patent_type') {

		var name = $("#patent_type").val().trim();
	
		

		if (name == '' ) {
			alert('Для добавления вида охранного документа все поля должны быть заполнены!');
			return;
		}

		$.post("./ajax_add_item.php", {
			object : object,
			name : name
		}, function(data) {
			if (data.length > 0) {
			//	console.log("data:" + data);
				var json = jQuery.parseJSON(data);

			}
			var select = document.getElementById(idOfSelectElement);
			var optionElement = document.createElement("option");
			optionElement.setAttribute("value", json.id);

			optionElement.setAttribute("selected", "selected");
			optionElement.appendChild(document.createTextNode(json.name));
			select.appendChild(optionElement);
			sortSelect(select);

			$("#" + idOfDivForPlace).slideUp();
			document.getElementById(idOfCheckBoxForShow).checked = false;

		});
	}

}

function sortSelect(selElem) {
	var tmpAry = new Array();
	for ( var i = 0; i < selElem.options.length; i++) {
		tmpAry[i] = new Array();
		tmpAry[i][0] = selElem.options[i].text;
		tmpAry[i][1] = selElem.options[i].value;
		tmpAry[i][2] = selElem.options[i].selected;
	}
	tmpAry.sort();
	while (selElem.options.length > 0) {
		selElem.options[0] = null;
	}
	for ( var i = 0; i < tmpAry.length; i++) {
		var op = new Option(tmpAry[i][0], tmpAry[i][1]);
		op.selected = tmpAry[i][2];
		selElem.options[i] = op;
	}
	return;
}

function checkOnlyOneSelected(obj) {
	// alert(obj.id);
	// return;
	var currentEl = document.getElementById(obj.id);
	var elements = document.getElementsByClassName('my_publ');
	var numberChecked = 0;
	var thisElIndex = 0;
	for ( var i = 0; i < elements.length; i++) {

		if (elements[i].checked) {
			numberChecked++;
		}
		if (elements[i].id == obj.id) {
			thisElIndex = i;
		}
	}

	if (numberChecked > 1) {

		alert("Отметить можно только одного автора в качестве себя!");

		currentEl.checked = false;
		return;

	} else {

		currentEl.value = +thisElIndex;
		var parTr = currentEl.parentNode.parentNode;
		var inputsEls = parTr.getElementsByTagName('input');
		var curUser = getCurrentUser();
		for ( var i = 0; i < inputsEls.length; i++) {
			if (inputsEls[i].name == 'c07_authors_lastname[]') {
				inputsEls[i].value = curUser.l_name;
			} else if (inputsEls[i].name == 'c07_authors_firstname[]') {
				inputsEls[i].value = curUser.f_name.substring(0, 1);
			} else if (inputsEls[i].name == 'c07_authors_patrname[]') {
				inputsEls[i].value = curUser.p_name.substring(0, 1);
			}
		}

		textareaEls = parTr.getElementsByTagName('textarea');

		for ( var i = 0; i < textareaEls.length; i++) {

			if (textareaEls[i].name == 'c08_place_working_authors[]') {
				textareaEls[i].value = curUser.org;
			}
		}

	}

}

function delFunct(thisElement) {
	parentTr = thisElement.parentNode.parentNode;
	previousTr = parentTr.previousSibling;
	previousTr.id = parentTr.id;
	parentTr.remove();
}

function addItem(last_tr_id_label) {
	var lastTr = document.getElementById(last_tr_id_label);

	var newInputRow = document.createElement("tr");
	var tdElement;
	var fieldInput;
	var deleteRef;

	var deleteBut;

	if (last_tr_id_label == "last_author") {

		addAuthor(newInputRow);

	} else if (last_tr_id_label == "last_reference") {

		addReference(newInputRow);

	} else {
		alert("Операция не поддерживается");
	}

	tdElement = document.createElement("td");
	deleteRef = document.createElement("a");
	deleteRef.setAttribute("onclick", "delFunct(this);");

	deleteBut = document.createElement("button");
	deleteBut.appendChild(document.createTextNode("Удалить"));
	deleteRef.appendChild(deleteBut);
	tdElement.appendChild(deleteRef);
	newInputRow.appendChild(tdElement);

	lastTr.parentNode.insertBefore(newInputRow, lastTr.nextSibling);
	lastTr.id = null;
	newInputRow.id = last_tr_id_label;

	return false;
}

function addAuthor(newInputRow) {
	var tdElement = document.createElement("td");
	newInputRow.appendChild(tdElement);

	var tdElement = document.createElement("td");
	var fieldInput = document.createElement("input");
	fieldInput.setAttribute("id", "c07_authors_me1"
			+ Math.floor(Math.random() * 1000000));
	fieldInput.setAttribute("type", "checkbox");
	fieldInput.setAttribute("name", "c07_authors_me[]");
	// fieldInput.setAttribute("value", "");
	fieldInput.setAttribute("class", "my_publ");
	fieldInput.setAttribute("onclick", "checkOnlyOneSelected(this);");
	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);

	tdElement = document.createElement("td");
	fieldInput = document.createElement("input");
	fieldInput.setAttribute("type", "text");
	fieldInput.setAttribute("name", "c07_authors_lastname[]");
	fieldInput.setAttribute("size", "30");
	fieldInput.setAttribute("required", "required");
	fieldInput.setAttribute("class", "req_input");
	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);

	tdElement = document.createElement("td");
	fieldInput = document.createElement("input");
	fieldInput.setAttribute("type", "text");
	fieldInput.setAttribute("name", "c07_authors_firstname[]");
	fieldInput.setAttribute("size", "2");
	fieldInput.setAttribute("maxlength", "1");
	fieldInput.setAttribute("required", "required");
	fieldInput.setAttribute("class", "req_input");
	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);

	tdElement = document.createElement("td");
	fieldInput = document.createElement("input");
	fieldInput.setAttribute("type", "text");
	fieldInput.setAttribute("name", "c07_authors_patrname[]");
	fieldInput.setAttribute("size", "2");
	fieldInput.setAttribute("maxlength", "1");
	fieldInput.setAttribute("required", "required");
	fieldInput.setAttribute("class", "req_input");
	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);

	tdElement = document.createElement("td");

	// <textarea {$readonly} name='c08_place_working_authors[]' cols='45'
	// rows='3'></textarea></td>
	fieldInput = document.createElement("textarea");
	fieldInput.setAttribute("name", "c08_place_working_authors[]");
	fieldInput.setAttribute("cols", "45");
	fieldInput.setAttribute("rows", "3");
	fieldInput.setAttribute("required", "required");
	fieldInput.setAttribute("class", "req_input");
	fieldInput.value = "Казахский научно-исследовательский институт онкологии и радиологии МЗ РК";

	// fieldInput = document.createElement("input");
	// fieldInput.setAttribute("type", "text");
	// fieldInput.setAttribute("name", "c08_place_working_authors[]");
	// fieldInput.setAttribute("size", "60");

	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);

}

function addReference(newInputRow) {
	var tdElement = document.createElement("td");
	var selectElement = document.createElement("select");
	selectElement.setAttribute("name", "reference_kaz_type_id[]");
	selectElement.setAttribute("style", "width: 300px");

	var array = getArraReferences();

	for ( var i = 0; i < array.length; i++) {
		var optionElement = document.createElement("option");
		optionElement.setAttribute("value", array[i][0]);
		optionElement.appendChild(document.createTextNode(array[i][1]));
		selectElement.appendChild(optionElement);
	}

	tdElement.appendChild(selectElement);
	newInputRow.appendChild(tdElement);

	// name='c19_list_references_kazakh[]' cols='60' rows='3'>

	tdElement = document.createElement("td");
	fieldInput = document.createElement("textarea");
	fieldInput.setAttribute("name", "c19_list_references_kazakh[]");
	fieldInput.setAttribute("cols", "60");
	fieldInput.setAttribute("rows", "3");
	fieldInput.setAttribute("required", "required");
	fieldInput.setAttribute("class", "req_input");
	tdElement.appendChild(fieldInput);
	newInputRow.appendChild(tdElement);
}

function exampleHowToAddElements(obj) {
	/*
	 * var filedsetEl = document.createElement("fieldset"); var legendEl =
	 * document.createElement("legend"); var tableEl =
	 * document.createElement("table"); var newInputRow; var tdElement; var
	 * fieldInput; var nameOrIdOfInput; var labelInput;
	 * 
	 * currentEl.appendChild(filedsetEl); filedsetEl.appendChild(legendEl);
	 * legendEl.appendChild(document.createTextNode("Новый журнал"));
	 * filedsetEl.appendChild(tableEl);
	 * 
	 * nameOrIdOfInput = "j_country"; labelInput = "Страна издания журнала";
	 * newInputRow = document.createElement("tr"); tdElement=
	 * document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("size", "50");
	 * fieldInput.setAttribute("id", nameOrIdOfInput);
	 * fieldInput.setAttribute("type", "text"); fieldInput.setAttribute("name",
	 * nameOrIdOfInput); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 * 
	 * nameOrIdOfInput = "j_issn"; labelInput = "ISSN"; newInputRow =
	 * document.createElement("tr"); tdElement= document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("size", "50");
	 * fieldInput.setAttribute("id", nameOrIdOfInput);
	 * fieldInput.setAttribute("type", "text"); fieldInput.setAttribute("name",
	 * nameOrIdOfInput); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 * 
	 * nameOrIdOfInput = "j_name"; labelInput = "Полное наименование журнала";
	 * newInputRow = document.createElement("tr"); tdElement=
	 * document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("size", "50");
	 * fieldInput.setAttribute("id", nameOrIdOfInput);
	 * fieldInput.setAttribute("type", "text"); fieldInput.setAttribute("name",
	 * nameOrIdOfInput); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 * 
	 * nameOrIdOfInput = "j_periodicity"; labelInput = "Периодичность выхода
	 * журнала"; newInputRow = document.createElement("tr"); tdElement=
	 * document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("size", "50");
	 * fieldInput.setAttribute("id", nameOrIdOfInput);
	 * fieldInput.setAttribute("type", "text"); fieldInput.setAttribute("name",
	 * nameOrIdOfInput); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 * 
	 * nameOrIdOfInput = "j_izdatelstvo_mesto_izdaniya"; labelInput =
	 * "Издательство, место издания журнала"; newInputRow =
	 * document.createElement("tr"); tdElement= document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("size", "50");
	 * fieldInput.setAttribute("id", nameOrIdOfInput);
	 * fieldInput.setAttribute("type", "text"); fieldInput.setAttribute("name",
	 * nameOrIdOfInput); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 * 
	 * 
	 * nameOrIdOfInput = "j_submit"; labelInput = ""; newInputRow =
	 * document.createElement("tr"); tdElement= document.createElement("td");
	 * tdElement.appendChild(document.createTextNode(labelInput));
	 * newInputRow.appendChild(tdElement); tdElement=
	 * document.createElement("td"); fieldInput =
	 * document.createElement("input"); fieldInput.setAttribute("id",
	 * nameOrIdOfInput); fieldInput.setAttribute("type", "submit");
	 * fieldInput.setAttribute("name", nameOrIdOfInput);
	 * fieldInput.setAttribute("value", "Сохранить");
	 * fieldInput.setAttribute("onclick", "addAndSelectJournal(); return
	 * false;"); filedsetEl.appendChild(fieldInput);
	 * tdElement.appendChild(fieldInput); newInputRow.appendChild(tdElement);
	 * tableEl.appendChild(newInputRow);
	 */
}