$(function(){
    $(document)
        .on("click", ".group_delete", function(e){
            e.preventDefault();
            loaderOn();
            var groupid = $(this).data("groupid");
            deleteItem("Schedule_Group", groupid, refreshGroupTable);
        })
        .on("click", ".group_archive", function(e){
            e.preventDefault();
            loaderOn();
            var group_id = $(this).data("groupid");
            updateActive("Schedule_Group", group_id, 0, refreshGroupTable);
        })
        .on("click", ".group_edit", function(e){
            e.preventDefault();
            var groupid = $(this).data("groupid");
            getGroupPopup(groupid);
        })
        .on("click", ".popop_group_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveGroup();
        })
        .on("click", ".group_create", function(e){
            e.preventDefault();
            getGroupPopup(0);
        });
});


function saveGroup() {
    var Data = $("#createData").serialize();

    $.ajax({
        type: "GET",
        url: "",
        data: Data,
        success: function(responce){
            closePopup();
            refreshGroupTable();
        }
    });
}


function refreshGroupTable(){
    $.ajax({
        type: "GET",
        url: "groups",
        async: false,
        data: {
            action: "refreshGroupTable",
        },
        success: function(responce) {
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}

function getGroupPopup(groupid) {
    $.ajax({
        type: "GET",
        url: "groups",
        data: {
            action: "updateForm",
            groupid: groupid
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}