var root = $("#rootdir").val();

$(document).ready(function(){

    $("body")
        .on("click", ".areas_assignments", function(e){
            e.preventDefault();

            var modelId = $(this).data("model-id");
            var modelName = $(this).data("model-name");

            showAssignmentsPopup(modelId, modelName);
        });

    $(".popup")

        /**
         * Создание новой связи по перемещению филиала из одного списка в другой
         */
        .on("click", "#append_assignment", function(e){
            e.preventDefault();

            var modelId = $("#model-id").val();
            var modelName = $("#model-name").val();

            var selectedAreas = $("#areas-list").find("option:selected");
            var assignmentsList = $("#areas-assignments");

            $.each(selectedAreas, function(key, option){
                var selectedAreaId = $(option).val();

                if(assignmentsList.find("option[value="+selectedAreaId+"]").length == 0)
                {
                    appendAreaAssignment(modelId, modelName, selectedAreaId, function (response) {
                        assignmentsList.append("<option data-area-id='" + selectedAreaId + "' value='" + response.id + "'>" + response.title + "</option>");

                        var areasSpanSelector = modelName + "_" + modelId;
                        var areasSpan = $("span[data-areas="+areasSpanSelector+"]");

                        if(areasSpan.length != 0)
                        {
                            if (areasSpan.find("span").length == 0)
                            {
                                areasSpan.append("<span data-area-id='"+selectedAreaId+"'>"+response.title+"</span>");
                            }
                            else
                            {
                                areasSpan.append("<span data-area-id='"+selectedAreaId+"'><br/>"+response.title+"</span>");
                            }
                        }
                    });
                }
            });
        })
        .on("click", "#area_assignment_delete", function(e) {
            e.preventDefault();

            var modelId = $("#model-id").val();
            var modelName = $("#model-name").val();

            var assignments = $("#areas-assignments").find("option:selected");

            $.each(assignments, function(key, option){
                var selectedAreaId = $(option).val();

                deleteAreaAssignment(modelId, modelName, selectedAreaId, function(response){
                    $(option).remove();

                    var areasSpanSelector = modelName + "_" + modelId;
                    var areasSpan = $("span[data-areas="+areasSpanSelector+"]");

                    if(areasSpan.length != 0)
                    {
                        var areaSpan = areasSpan.find("span[data-area-id="+ selectedAreaId +"]");

                        if(areaSpan.length = 1)
                        {
                            $(areaSpan).remove();
                        }
                    }
                });
            });
        });

});


function deleteAreaAssignment(modelId, modelName, areaId, func) {
    loaderOn();

    $.ajax({
        type: "GET",
        url: root + "/user",
        data: {
            action: "deleteAreaAssignment",
            model_id: modelId,
            model_name: modelName,
            area_id: areaId
        },
        success: function(response) {
            func(response);
            loaderOff();
        }
    });
}


/**
 * Создание связи объекта и филиала
 *
 * @date 21.01.2019 17:25
 *
 * @param modelId - id объекта
 * @param modelName - название класса объекта
 * @param areaId - id филиала с которым создается связь
 * @param func
 */
function appendAreaAssignment(modelId, modelName, areaId, func) {
    loaderOn();

    $.ajax({
        type: "GET",
        url: root + "/user",
        data: {
            action: "appendAreaAssignment",
            model_id: modelId,
            model_name: modelName,
            area_id: areaId
        },
        dataType: "json",
        success: function(response) {
            func(response);
            loaderOff();
        }
    });
}


/**
 * Открытие всплывающего окна с редактированием связей объекта и филиалов
 *
 * @date 21.01.2019 14:32
 *
 * @param modelId
 * @param modelName
 */
function showAssignmentsPopup(modelId, modelName) {
    loaderOn();

    $.ajax({
        type: "GET",
        url: root + "/user",
        data: {
            action: "showAssignmentsPopup",
            model_id: modelId,
            model_name: modelName
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        }
    });
}