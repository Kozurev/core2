<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Типы уроков</h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Тип</th>
                    <th>Отображение данных в статистике</th>
                </tr>
                <xsl:apply-templates select="schedule_lesson_type" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson_Type" class="link">
                    Добавить тип
                </a>
            </button>
        </div>
    </xsl:template>


    <xsl:template match="schedule_lesson_type">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>

            <td>
                <xsl:choose>
                    <xsl:when test="statistic = 1">
                        ДА
                    </xsl:when>
                    <xsl:otherwise>
                        НЕТ
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson_Type&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Lesson_Type" data-model_id="{id}" class="delete deleteLink"></a>
            </td>

        </tr>
    </xsl:template>


</xsl:stylesheet>