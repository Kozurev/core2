$(function(){
    $("body")
        .on("click", ".statistic_show", function(e){
            e.preventDefault();
            var from = $(".finances_calendar").find("input[name=date_from]").val();
            var to = $(".finances_calendar").find("input[name=date_to]").val();
            loaderOn();
            showStatistic(from, to);
        });
});


function showStatistic(from, to) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "refresh",
            date_from: from,
            date_to: to
        },
        success: function(responce){
            var Page = $(".statistic");
            Page.empty();
            Page.append(responce);
            loaderOff();
        }
    });
}