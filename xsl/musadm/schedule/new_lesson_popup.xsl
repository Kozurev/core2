<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                var timestep = $("#timestep").val();

                $("#createData").validate({
                    rules: {
                        teacherId:   {min: 1},
                        timeFrom:    {required: true },
                        timeTo:      {required: true },
                        typeId:      {required: true, min: 1},
                    },
                    messages: {
                        teacherId:  { min: "Необходимо указать преподавателя" },
                        timeFrom:   { required: "Это поле обязательноое к заполнению" },
                        timeTo:     { required: "Это поле обязательноое к заполнению" },
                        typeId:     { min: "Это поле обязательноое к заполнению"},
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="center">
                <h3>Добавление урока в <xsl:value-of select="schedule_type" /> расписание</h3>
            </div>

            <input type="hidden" value="{//timestep}" id="timestep" />

            <xsl:choose>
                <xsl:when test="teacher_id != ''">
                    <input type="hidden" name="teacherId" value="{current_user/id}" />
                </xsl:when>
                <xsl:otherwise>
                    <div class="column">
                        <span>Учитель</span><span style="color:red" >*</span>
                    </div>
                    <div class="column">
                        <select class="form-control" name="teacherId">
                            <option value="0">...</option>
                            <xsl:for-each select="user">
                                <option value="{id}">
                                    <xsl:if test="is_absent = 1">
                                        <xsl:attribute name="style">color:red</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="surname" />
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="name" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                    <hr/>
                </xsl:otherwise>
            </xsl:choose>

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
                <input class="form-control" name="timeFrom" type="time"  />
            </div>
            <hr/>

            <div class="column">
                <span>Время окончания</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" name="timeTo" type="time"  />
            </div>
            <hr/>

            <xsl:if test="lesson_type = 2">
                <div class="column remember">
                    <span>Обсудить после урока следующий день занятий</span>
                </div>
                <div class="column remember">
                    <input class="form-control" name="is_create_task" type="checkbox" />
                </div>
                <hr/>
            </xsl:if>

            <div class="column">
                <span>Онлайн</span>
            </div>
            <div class="column">
                <input class="form-control" name="isOnline" type="checkbox" value="1" />
            </div>
            <hr/>


            <input type="hidden" name="user_group_id" value="{current_user/group_id}" />
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="classId" value="{class_id}" />
            <input type="hidden" name="insertDate" value="{date}" />
            <input type="hidden" name="areaId" value="{area_id}" />
            <input type="hidden" name="dayName" value="{day_name}" />
            <input type="hidden" name="lessonType" value="{lesson_type}" />
            <input type="hidden" name="deleteDate" />
            <input type="hidden" value="Schedule_Lesson" name="modelName" />

            <button class="popop_schedule_lesson_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>



</xsl:stylesheet>