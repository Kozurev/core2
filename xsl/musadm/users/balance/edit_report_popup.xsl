<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Стоимость урока для клиента</span>
            </div>
            <div class="column">
                <input type="number" class="form-control" name="clientRate" value="{rep/client_rate}" />
            </div>

            <div class="column">
                <span>Выплата преподавателю</span>
            </div>
            <div class="column">
                <input type="number" class="form-control" name="teacherRate" value="{rep/teacher_rate}" />
            </div>

            <!--<div class="column">-->
                <!--<span>Прибыль</span>-->
            <!--</div>-->
            <!--<div class="column">-->
                <!--<input type="number" class="form-control" name="totalRate" value="{rep/total_rate}" />-->
            <!--</div>-->

            <input type="hidden" name="id" value="{rep/id}" />
            <input type="hidden" name="modelName" value="Schedule_Lesson_Report" />

            <button class="btn btn-default report_data_submit">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>