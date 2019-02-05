$(document).ready(function() {
	
	/* ===== Affix Sidebar ===== */
	/* Ref: http://getbootstrap.com/javascript/#affix-examples */
    	
	$('#doc-menu').affix({
        offset: {
            top: ($('#header').outerHeight(true) + $('#doc-header').outerHeight(true)) + 45,
            bottom: ($('#footer').outerHeight(true) + $('#promo-block').outerHeight(true)) + 75
        }
    });
    
    /* Hack related to: https://github.com/twbs/bootstrap/issues/10236 */
    $(window).on('load resize', function() {
        $(window).trigger('scroll'); 
    });

    /* Activate scrollspy menu */
    $('body').scrollspy({target: '#doc-nav', offset: 100});
    
    /* Smooth scrolling */
	$('a.scrollto').on('click', function(e){
        //store hash
        var target = this.hash;    
        e.preventDefault();
		$('body').scrollTo(target, 800, {offset: 0, 'axis':'y'});

	});
	
    
    /* ======= jQuery Responsive equal heights plugin ======= */
    /* Ref: https://github.com/liabru/jquery-match-height */
    
     $('#cards-wrapper .item-inner').matchHeight();
     $('#showcase .card').matchHeight();
     
    /* Bootstrap lightbox */
    /* Ref: http://ashleydw.github.io/lightbox/ */

    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(e) {
        e.preventDefault();
        $(this).ekkoLightbox();
    });

    //Кнопка закрытия модального окна (стандартный обработчик не работает)
    $(".modal").on("click", ".close", function(e){
        e.preventDefault();
        var popup = $(this).parent().parent().parent().parent();
        popup.hide("slow");
        //popup.find(".modal-title").empty();
        //popup.find(".content").empty();
        return false;
    });


    $(".table").tablesorter();

    $(".masked-phone").mask("7(999) 999-9999");

});


var loaderTimeout = 100;
var messageTimeout = 3000;

//Большое модальное окно для форм и изображений
var popupSmall = $("#ekkoLightbox-640");

//Маленькое модальное окно для сообщений
var popupBig = $("#ekkoLightbox-641");


function showPopup(title, data) {
    popupBig.find(".modal-title").text(title);
    popupBig.find(".content").html(data);
    popupBig.show("slow");
}

function closePopup() {
    popupBig.hide("slow");
    popupBig.find(".modal-title").empty();
    popupBig.find(".content").empty();
}

//Запуск лоадера
function loaderOn(){
    var bodyHeight = ($(window).height() - $(".loader").outerHeight()) / 2;
    if(bodyHeight < window.innerHeight) bodyHeight = window.innerHeight;
    $(".loader").css("height", bodyHeight);
    $(".loader").show();
}

//Отключение лоадера
function loaderOff(){
    setTimeout('$(".loader").hide()', loaderTimeout);
}


function showError(errorText) {
    popupSmall.find(".modal-title").text("Ошибка");
    popupSmall.find(".content").html(errorText);
    popupSmall.addClass("error");
    popupSmall.slideDown("slow");
}

function showMessage(title, text, status) {
    popupSmall.find(".modal-title").text(title);
    popupSmall.find(".content").html(text);
    if(status == "")    status = "default";
    popupSmall.addClass(status);
    popupSmall.slideDown("slow");
    setTimeout('$("#ekkoLightbox-640").slideUp("slow");', messageTimeout);
}