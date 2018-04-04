

$(function(){

	if(window.location.hash == "")
	{
		window.location.hash = "#/admin?menuTab=Structure&menuAction=show";
	}

	$(document)
		//Обновление рабочей области административного раздела
		.on("click", ".link", function(e){
			e.preventDefault();
			var link = $(this).attr("href");
			window.location.hash = link;
		})
		//Обработка изменения активности элемента
		.on("click", ".activeCheckbox", function(e){
			var model_name = $(this).attr("model_name");
			var model_id = $(this).attr("model_id");
			var value = $(this).prop("checked");
			updateActive(model_name, model_id, value);
		})
		//Обработчик удаления элемента
		.on("click", ".delete", function(e){
			e.preventDefault();
			var model_name = $(this).data("model_name");
			var model_id = $(this).data("model_id");
			deleteItem(model_name, model_id);
		})
		//Сохранение даных
		.on("click", ".submit", function(e){
			e.preventDefault();

			var data = $("form").serialize();
			updateItem(data);
		})
		//Добавление поля для дополнительного свойства
		.on("click", ".add_new_value", function(e){
			e.preventDefault();

			var aBlocks = $(this).parent().find(".field");
			var lastBlock = $(aBlocks)[aBlocks.length - 1];
			var appendedBlock = $(lastBlock).clone();

			if($(aBlocks).length == 1)
			{
				appendedBlock.append('<div class="delete_block"></div>');
			}

			var button = $(this).parent().find(".add_new_value").clone();

			$(this).parent().append(appendedBlock);
			$(this).parent().append(button);

			//Удаление лишней кнопки "Добавить"
			var aButtons = $(this).parent().find(".add_new_value");
			$(aButtons)[0].remove();
		})
		//Удаление поля дополнительного свойства
		.on("click", ".delete_block", function(){
			$(this).parent().remove();
		})
		.on("click", ".next_page", function(e){
			e.preventDefault();
			var current_page = Number($("#current_page").text());
			var count_pages = Number($("#count_pages").text());
			if(current_page == count_pages)	return;
			//var hash = window.location.hash;
			setPage(current_page);
		})
		.on("click", ".prev_page", function(e){
			e.preventDefault();
            var current_page = Number($("#current_page").text());
            var count_pages = Number($("#count_pages").text());
            if(current_page == 1)	return;
            setPage(current_page-2);
		});
});


function setPage(page) {
    var hash = window.location.hash;
    if(hash.indexOf("&page") >= 0)	hash = hash.substring(0, hash.indexOf("&page"));
    hash += "&page=" + page;
    window.location.hash = hash;
}

/**
*	Перезагрузка рабочей области административного раздела
*	обработка перехода по ссылкам
*	@param hash - хэш 
*/
function reloadMain(hash){
	loaderOn();
	link = hash.substr(1); //форматирование хеша (удаление из строки первого символа '#'')

	$.ajax({
		type: "GET",
		url: link + "&ajax=1", 	
		success: function(data){
			$(".main").html(data);
			setTimeout("loaderOff()", 200);
		}
	});
}


/**
*	Изменение активности структуры или элемента
*	@param model_name - название объекта (Structure, Structure_Item и т.д.)
*	@param model_id - id объекта
*	@param value - значение активности true/false
*/
function updateActive(model_name, model_id, value){
	loaderOn();	

	var link = "/admin?menuTab=Main&menuAction=updateActive&ajax=1";
	link += "&model_name=" + model_name; 
	link += "&model_id=" + model_id;
	link += "&value=" + value;
	link += "&ajax=1";

	console.log(link);

	$.ajax({
		type: "GET",
		url: link,
		success: function(answer){
			setTimeout("loaderOff()", 200);
			if(answer != "0")
				alert("Ошибка: " + answer);
		}
	});
}


/**
*	Удаление объекта
*/
function deleteItem(model_name, model_id){

	var link = "/admin?menuTab=Main&menuAction=deleteAction&ajax=1";
	link += "&model_name=" + model_name;
	link += "&model_id=" + model_id;

	var agree = confirm("Вы действительно хотите удалить объект?");
	if(agree != true) return;

	loaderOn();

	$.ajax({
		type: "GET",
		url: link,
		success: function(answer){
			reloadMain(window.location.hash);
			setTimeout("loaderOff()", 200);
			if(answer != "0")
				alert("Ошибка: " + answer);
		}
	});
}


function updateItem(objectData){
	loaderOn();

	var link = "/admin?menuTab=Main&menuAction=updateAction&ajax=1&" + objectData;

	$.ajax({
		type: "GET",
		url: link,
		success: function(answer){
			//reloadMain("#" + link);
			window.history.back();
			setTimeout("loaderOff()", 200);
			if(answer != "0")
				alert("Ошибка: " + answer);
		}
	});
}


window.onhashchange = function(){
	reloadMain(window.location.hash);
}

window.onload = function(){
	reloadMain(window.location.hash);
}

//Запуск лоадера
function loaderOn(){
	$(".loader").show();
}

//Отключение лоадера
function loaderOff(){
	$(".loader").hide();
}