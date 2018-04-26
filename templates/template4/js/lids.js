$(function(){
    $(document)
        .on("click", ".lid_submit", function(e){
            e.preventDefault();
            loaderOn();
            var form = $("form[name='lid_form']");
            var data = $(form).serialize();
            saveData("./admin?menuTab=User&menuAction=updateAction&ajax=1", refreshLidTable);
        });
});

function refreshLidTable(){
    $.ajax({
        type: "GET",
        url: "lids",
        data: {
            action: "refreshLidTable"
        },
        success: function(responce){
            $(".page").empty();
            $(".page").append(responce);
            //$("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}