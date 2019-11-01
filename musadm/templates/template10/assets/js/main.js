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

    $(".masked-phone").mask("+79999999999");

});


var loaderTimeout = 100;
var messageTimeout = 3000;


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

function notificationSuccess(str) {
    showNotification(str, 'success');
}

function notificationError(str) {
    showNotification(str, 'error');
}

function showNotification(str, type) {
    var
        box = $("#message_box"),
        text = $('#message_text')
        status = 'notification-' + type;

    box.removeClass('notification-success');
    box.removeClass('notification-error');

    text.html(str);
    box.addClass(status);
    box.fadeIn(500).delay(3000).fadeOut(500, function(){});
}