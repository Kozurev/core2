<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row">
            <div class="col-md-2 center">
                <span>Дата</span>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="date" name="date" />
            </div>
            <div class="col-md-3">
                <span>Преподаватель</span>
            </div>
            <div class="col-md-3">
                <select class="form-control" name="teacherId">
                    <xsl:for-each select="teacher">
                        <option value="{id}">
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 center">
                <button class="btn btn-primary"
                        onclick="Schedule.getNearestTeacherTime($('select[name=teacherId]').val(), $('input[name=date]').val(), '{lesson_duration}', teacherNearestFreeTimeCallback)">
                    Подобрать время
                </button>
            </div>
        </div>
        <div class="row teacherTime center">

        </div>
        <div class="row saveBtnRow" style="display:none">
            <div class="col-md-12">
                <button class="btn btn-default" onclick="event.preventDefault; saveClientLesson()">Забронировать</button>
            </div>
        </div>

        <input type="hidden" name="clientId" value="{client/id}" />
        <input type="hidden" name="areaId" value="{area_id}" />
<!--        <input type="hidden" name="lessonDuration" value="{lesson_duration}" />-->
    </xsl:template>

</xsl:stylesheet>