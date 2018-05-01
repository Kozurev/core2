<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Основное расписание</h3>
            <table class="table">
                <th>id</th>
                <th>День</th>
                <th>Время</th>
                <th>Учитель</th>
                <th>Ученик</th>
                <th>Группа</th>
                <th>Действия</th>
                <xsl:apply-templates select="schedule_lesson" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson" class="link">
                    Добавить урок
                </a>
            </button>
        </div>
    </xsl:template>


    <xsl:template match="schedule_lesson">
        <tr>
            <td><xsl:value-of select="id" /></td>

            <td>
                <xsl:call-template name="getDayName">
                    <xsl:with-param name="eng" select="day_name" />
                </xsl:call-template>
            </td>

            <td>
                <xsl:value-of select="time_from" /> <br/>
                <xsl:value-of select="time_to" />
            </td>

            <td>
                <xsl:value-of select="teacher/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="teacher/name" />
            </td>

            <td>
                <xsl:value-of select="client/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="client/name" />
            </td>

            <td>
                <xsl:value-of select="group/title" />
            </td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Lesson" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template name="getDayName">
        <xsl:param name="eng" />

        <xsl:choose>
            <xsl:when test="$eng = 'Monday'">
                Понедельник
            </xsl:when>
            <xsl:when test="$eng = 'Tuesday'">
                Вторник
            </xsl:when>
            <xsl:when test="$eng = 'Wednesday'">
                Среда
            </xsl:when>
            <xsl:when test="$eng = 'Thursday'">
                Четверг
            </xsl:when>
            <xsl:when test="$eng = 'Friday'">
                Пятница
            </xsl:when>
            <xsl:when test="$eng = 'Saturday'">
                Суббота
            </xsl:when>
            <xsl:when test="$eng = 'Sunday'">
                Воскресенье
            </xsl:when>
        </xsl:choose>

    </xsl:template>

</xsl:stylesheet>