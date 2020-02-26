<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row">
            <div class="col-md-6">
                <input class="form-control" type="date" name="date" />
            </div>
            <div class="col-md-6">
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
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12 center">
                <button class="btn btn-primary hidden"
                        id="getNearestTeacherTimeBtn"
                        onclick="Schedule.getNearestTeacherTime($('select[name=teacherId]').val(), $('input[name=date]').val(), '{lesson_duration}', teacherNearestFreeTimeCallback)">
                    Подобрать время
                </button>
            </div>
        </div>
        <div class="row teacherTime center" style="margin-top: 10px" >

        </div>
        <div class="row saveBtnRow" style="display:none">
            <div class="col-md-12">
                <button class="btn btn-default" onclick="event.preventDefault; saveClientLesson()">Забронировать</button>
            </div>
        </div>

        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <span style="font-size: 13px">
                    Вы можете самостоятельно управлять своим графиком. Чтобы поставить новый урок, нажмите на кнопку. Программа подберет варианты свободного
                    времени преподавателя автоматически. Функция работает в тестовом режиме, поэтому если вы не нашли подходящего времени, то это совсем
                    не значит, что нет вариантов. Свяжитесь с администраторами и с вами обговорят график более детально +79092012550
                </span>
            </div>
        </div>

        <input type="hidden" name="clientId" value="{client/id}" />
        <input type="hidden" name="areaId" value="{area_id}" />

        <script>
            $(function(){
                $('input[name=date]').on('change', function() {
                    $('#getNearestTeacherTimeBtn').trigger('click');
                });
            });
        </script>
    </xsl:template>

</xsl:stylesheet>