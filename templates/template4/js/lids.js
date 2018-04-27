$(function(){
    $(document)
        .on("click", ".lid_submit", function(e){
            e.preventDefault();
            loaderOn();
            var form = $("form[name='lid_form']");
            var data = $(form).serialize();
            //saveData("./admin?menuTab=User&menuAction=updateAction&ajax=1", refreshLidTable);
            saveLid(data);
        })
        .on("click", ".add_lid_comment", function(e){
            e.preventDefault();
            var lidid = $(this).data("lidid");
            getCommentPopup(lidid);
        })
        .on("click", ".popop_lid_comment_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("./admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshLidTable);
        })
        .on("change", ".lid_status", function(){
            loaderOn();
            var lidid = $(this).data("lidid");
            var statusid = $(this).val();
            changeStatus(lidid, statusid, refreshLidTable);
        })
        .on("change", ".lid_date", function(){
            loaderOn();
            var lidid = $(this).data("lidid");
            var date = $(this).val();
            changeDate(lidid, date, loaderOff);
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

function getCommentPopup(lidid){
    $.ajax({
        type: "GET",
        url: "lids",
        data: {
            action: "add_note_popup",
            model_id: lidid
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function saveLid(data){
    $.ajax({
        type: "GET",
        url: "lids?action=save_lid",
        data: data,
        success: function(responce){
            refreshLidTable();
        }
    });
}

function changeStatus(lidid, statusid, func){
    $.ajax({
        type: "GET",
        url: "lids",
        data: {
            action: "changeStatus",
            model_id: lidid,
            status_id: statusid
        },
        success: function(responce){
            func();
        }
    });
}

function changeDate(lidid, date, func){
    $.ajax({
        type: "GET",
        url: "lids",
        data: {
            action: "changeDate",
            model_id: lidid,
            date: date
        },
        success: function(responce){
            func();
        }
    });
}