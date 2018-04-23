$(function(){
    $(document).on("click", ".user_search_submit", function(){
        var search_data = $(".user_search_input").val();
        var group_id = $("#group_id").val();
        var link = "admin?menuTab=User&menuAction=show&group_id=" + group_id + "&search=" + search_data + "&parent_id=" + group_id + "&parent_name=User_Group";
        if(search_data == "")   return;
        window.location.hash = "#" + link;
    });
});