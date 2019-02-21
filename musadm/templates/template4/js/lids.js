"use strict";
var root = $("#rootdir").val();

$(function () {
    $(document)
        .on("click", ".lid_submit", function (e) {
            e.preventDefault();
            loaderOn();
            var data = "";
            data += "surname=" + $("input[name=surname]").val();
            data += "&comment=" + $("textarea[name=comment]").val();
            data += "&name=" + $("input[name=name]").val();
            data += "&number=" + $("input[name=number]").val();
            data += "&vk=" + $("input[name=vk]").val();
            data += "&status_id=" + $("select[name=status_id]").val();
            data += "&area_id=" + $("select[name=area_id]").val();
            data += "&control_date=" + $("input[name=control_date]").val();
            data += "&source=" + $("input[name=source]").val();
            closePopup();
            saveLid(data, refreshLidTable);
        })
        .on("click", ".add_lid_comment", function (e) {
            e.preventDefault();
            var lidid = $(this).data("lidid");
            getCommentPopup(lidid);
        })
        .on("click", ".popop_lid_comment_submit", function (e) {
            e.preventDefault();
            loaderOn();
            saveData("Main", function (response) {
                refreshLidTable();
            });
        })
        .on("change", ".lid_status", function () {
            loaderOn();
            var lidid = $(this).data("lidid");
            var statusid = $(this).val();
            changeStatus(lidid, statusid, refreshLidTable);
        })
        .on("change", ".lid_date", function () {
            loaderOn();
            var lidid = $(this).data("lidid");
            var date = $(this).val();
            changeDate(lidid, date, loaderOff);
        })
        .on("click", ".lids_show", function () {
            loaderOn();
            refreshLidTable();
        })
        .on("click", ".search", function(e) {
            e.preventDefault();
            loaderOn();
            var lidId = $("#search_id").val();
            if (lidId == "") {
                $("#search_id").addClass("error");
                alert("Введите номер лида в соответствующее поле");
                loaderOff();
                return false;
            }
            findLid(lidId);
        })
        .on("change", ".lid-area", function () {
            var areaId = $(this).val();
            var lidId = $(this).data("lid-id");
            updateLidArea(lidId, areaId, function (response) {
            });
        })
        .on("click", ".create_lid", function (e) {
            e.preventDefault();
            editLidPopup('');
        });
});


/**
 * Перезагрузка блока с классом lids
 */
function refreshLidTable() {

    var dateFrom = $("input[name=date_from]").val();
    var dateTo = $("input[name=date_to]").val();

    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "refreshLidTable",
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function (responce) {
            $(".lids").html(responce);
            loaderOff();
        }
    });
}


function updateLidArea(lidId, areaId, func) {
    $.ajax({
        type: "GET",
        url: root + "/lids",
        data: {
            action: "updateLidArea",
            lid_id: lidId,
            area_id: areaId
        },
        success: function (response) {
            func(response);
        }
    });
}


function editLidPopup(lidId) {
    $.ajax({
        type: "GET",
        url: root + "/lids",
        data: {
            action: "editLidPopup",
            lid_id: lidId
        },
        success: function (response) {
            showPopup(response);
        }
    });
}


/**
 * Поиск лида по id
 *
 * @param id - id лида
 */
function findLid(id) {
    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "refreshLidTable",
            lidid: id
        },
        success: function (responce) {
            //$(".lids").empty();
            $(".lids").html(responce);
            loaderOff();
        }
    });
}


/**
 * Открытие всплывающего окна для добавления комментария
 *
 * @param lidid - id лида
 */
function getCommentPopup(lidid) {
    $.ajax({
        type: "GET",
        url: root + "/lids",
        data: {
            action: "add_note_popup",
            model_id: lidid
        },
        success: function (responce) {
            showPopup(responce);
        }
    });
}


/**
 * Сохранение лида
 *
 * @param data - данные формы создания лида
 * @param func - функция, выполняющаяся после сохранения
 */
function saveLid(data, func) {
    $.ajax({
        type: "GET",
        url: root + "/lids?action=save_lid",
        async: false,
        data: data,
        success: function (responce) {
            func();
        }
    });
}


/**
 * Обработчик изменения статуса лида
 *
 * @param lidid - id лида
 * @param statusid - id статуса
 * @param func - функция выполняющаяся после выполнения
 */
function changeStatus(lidid, statusid, func) {
    $.ajax({
        type: "GET",
        url: root + "/lids",
        data: {
            action: "changeStatus",
            model_id: lidid,
            status_id: statusid
        },
        success: function (responce) {
            func();
        }
    });
}


/**
 * Изменение даты контроля лида
 *
 * @param lidid
 * @param date
 * @param func
 */
function changeDate(lidid, date, func) {
    $.ajax({
        type: "GET",
        url: root + "/lids",
        async: false,
        data: {
            action: "changeDate",
            model_id: lidid,
            date: date
        },
        success: function (responce) {
            func();
        }
    });
}