

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
		//Обработка изменения активности структуры или элемента
		.on("click", ".activeCheckbox", function(e){
			var model_name = $(this).attr("model_name");
			var model_id = $(this).attr("model_id");
			var value = $(this).prop("checked");
			updateActive(model_name, model_id, value);
		})
		.on("click", ".delete", function(e){
			e.preventDefault();
			var model_name = $(this).data("model_name");
			var model_id = $(this).data("model_id");
			deleteItem(model_name, model_id);
		})
		.on("click", ".submit", function(e){
			e.preventDefault();
			var data = $("form").serialize();
			updateItem(data);
		})
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
		.on("click", ".delete_block", function(){
			$(this).parent().remove();
		});
});


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

	var link = "/admin?menuTab=Structure&menuAction=updateActive";
	link += "&model_name=" + model_name; 
	link += "&model_id=" + model_id;
	link += "&value=" + value;
	link += "&ajax=1";

	console.log(link);

	$.ajax({
		type: "GET",
		url: link,
		success: function(){
			setTimeout("loaderOff()", 200);
		}
	});
}


/**
*	Удаление объекта
*/
function deleteItem(model_name, model_id){

	var link = "/admin?menuTab=Structure&menuAction=deleteAction";
	link += "&model_name=" + model_name;
	link += "&model_id=" + model_id;

	var agree = confirm("Вы действительно хотите удалить объект?");
	if(agree != true) return;

	loaderOn();

	$.ajax({
		type: "GET",
		url: link,
		success: function(){
			reloadMain(window.location.hash);
			setTimeout("loaderOff()", 200);
		}
	});
}


function updateItem(objectData){
	loaderOn();

	var link = "/admin?menuTab=Structure&menuAction=updateAction&" + objectData;

	$.ajax({
		type: "GET",
		url: link,
		success: function(data){
			//reloadMain("#" + link);
			window.history.back();
			setTimeout("loaderOff()", 200);
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