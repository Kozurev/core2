"use strict";
var root = $("#rootdir").val();

$(function() {
    $('body')
        .on('click', '.statistic_show', function(e) {
            e.preventDefault();
            loaderOn();

            var
                from =  $('.finances-calendar').find('input[name=date_from]').val(),
                to =    $('.finances-calendar').find('input[name=date_to]').val(),
                areaId= $('#statistic-areas-select').val();

            showStatistic(from, to, areaId, function(response) {
                $('.statistic').html(response);
                loaderOff();
            });
        })
        .on('change', '#statistic-areas-select', function() {
            loaderOn();

            var
                from =      $('.finances-calendar').find('input[name=date_from]').val(),
                to =        $('.finances-calendar').find('input[name=date_to]').val(),
                areaId =    $(this).val();

            showStatistic(from, to, areaId, function(response) {
                $('.statistic').html(response);
                loaderOff();
            });
        });
});


function showStatistic(from, to, areaId, func) {
    $.ajax({
        type: 'GET',
        url: root + '/statistic',
        data: {
            action: 'refresh',
            date_from: from,
            date_to: to,
            area_id: areaId
        },
        success: function(response){
            func(response);
        }
    });
}