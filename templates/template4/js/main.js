var loaderTime = 0;

$(function() {

    if (window.location.hash == "")
    {
        //window.location.hash = "#user/client";
    }

    $(document)
    //Обновление рабочей области административного раздела
        .on("click", ".link", function (e) {
            e.preventDefault();
            var link = $(this).attr("href");
            window.location.hash = link;
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

    //alert(link);
    if(link == "")
    {
        setTimeout("loaderOff()", loaderTime);
        return;
    }

    $.ajax({
        type: "GET",
        url: link,// + "&ajax=1",
        success: function(data){
            $(".page").html(data);
            setTimeout("loaderOff()", loaderTime);
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
    var bodyHeight = ($(window).height() - $(".loader").outerHeight()) / 2;
    if(bodyHeight < window.innerHeight) bodyHeight = window.innerHeight;
    //$("body").addClass("bodyLoader");
    $(".loader").css("height", bodyHeight);
    $(".loader").show();
}

//Отключение лоадера
function loaderOff(){
    //$("body").removeClass("bodyLoader");
    $(".loader").hide();
    $("#sortingTable").tablesorter();
}

function isAdmin() {
    // $.ajax({
    //
    // });
}