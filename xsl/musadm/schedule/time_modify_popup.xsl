<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <script>
            $(function(){
            $("#createData").validate({
            rules: {
            timeFrom:    {required: true},
            timeTo:      {required: true},
            },
            messages: {
            timeFrom:   { required: "Это поле обязательноое к заполнению", },
            timeTo:     { required: "Это поле обязательноое к заполнению", },
            }
            });
            });
        </script>


        <form name="createData" id="createData" action=".">
            <center><h2>Изменение времени</h2></center>

            <div class="column">
                <span>Время начала</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" name="timeFrom" type="time" />
            </div>
            <hr/>

            <div class="column">
                <span>Время окончания</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" name="timeTo" type="time" />
            </div>
            <hr/>

            <xsl:choose>
                <xsl:when test="model_name = 'Schedule_Current_Lesson'">
                    <input type="hidden" name="id" value="{schedule_current_lesson/id}" />
                    <input type="hidden" name="classId" value="{schedule_current_lesson/class_id}" />
                    <input type="hidden" name="date" value="{schedule_current_lesson/date}" />
                    <input type="hidden" name="areaId" value="{schedule_current_lesson/area_id}" />
                </xsl:when>
                <xsl:otherwise>
                    <input type="hidden" name="id" value="{schedule_lesson_timeModified/id}" />
                    <input type="hidden" name="lessonId" value="{schedule_lesson_timeModified/lesson_id}" />
                </xsl:otherwise>
            </xsl:choose>

            <input type="hidden" value="{model_name}" name="modelName" />

            <button class="popop_schedule_time_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>