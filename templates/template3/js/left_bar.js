$(function(){

    $(".children").hide();
    $("#7").show();

    $(".left_bar")
        .on("click", ".parent", function(){
            var childId = $(this).data("id");
            var childBlock = $("#"+childId);

            if(childBlock.css("display") == "none")
            {
                childBlock.show("fast");
            }
            else
            {
                childBlock.hide("fast");
            }


        });
});