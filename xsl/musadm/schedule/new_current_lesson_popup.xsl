<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        timeFrom:    {required: true},
                        timeTo:      {required: true},
                        typeId:      {required: true, min: 1},
                    },
                    messages: {
                        timeFrom:   { required: "Это поле обязательноое к заполнению", },
                        timeTo:     { required: "Это поле обязательноое к заполнению", },
                        typeId:     { min: "Это поле обязательноое к заполнению"},
                    }
                });
            });
        </script>


        <form name="createData" id="createData" action=".">

            <center><h2>Добавление урока в текущее расписание</h2></center>

            <div class="column">
                <span>Учитель</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <select class="form-control" name="teacherId">
                    <option value="0">...</option>
                    <xsl:for-each select="user[group_id = 4]">
                        <option value="{id}">
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <div class="column">
                <span>Тип урока</span>
            </div>
            <div class="column">
                <select class="form-control" name="typeId" >
                    <option value="0">...</option>
                    <xsl:for-each select="schedule_lesson_type">
                        <option value="{id}">
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <div class="column clients" style="display:none">
                <span>Ученик</span>
            </div>
            <div class="column clients" style="display:none">
                <select class="form-control" name="clientId" >
                </select>
            </div>
            <hr/>

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

            <input type="hidden" name="id" value="" />
            <input type="hidden" name="classId" value="{class_id}" />
            <input type="hidden" name="date" value="{date}" />
            <input type="hidden" name="areaId" value="{area_id}" />
            <input type="hidden" value="Schedule_Current_Lesson" name="modelName" />

            <button class="popop_schedule_lesson_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>