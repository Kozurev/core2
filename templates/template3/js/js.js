$(function(){
    $(document)
        .on("click", ".user_search_submit", function(){
            var search_data = $(".user_search_input").val();
            var group_id = $("#group_id").val();
            var link = "admin?menuTab=Main&menuAction=show&group_id=" + group_id + "&search=" + search_data + "&parent_id=" + group_id + "&parent_name=User_Group";
            if($("#createData").valid())
                window.location.hash = "#" + link;
        })
        .on("click", ".payment_search_submit", function(){
            var search_data = $(".payment_search_input").val();
            var link = "admin?menuTab=Payment&menuAction=show&search=" + search_data;
            if($("#createData").valid())
                window.location.hash = "#" + link;
        });
});