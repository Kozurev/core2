<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Текущее расписание</h3>
            <table class="table">
                <th>id</th>
                <th>Дата</th>
                <th>Время</th>
                <th>Учитель</th>
                <th>Ученик</th>
                <th>Группа</th>
                <th>Действия</th>
                <xsl:apply-templates select="schedule_current_lesson" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Current_Lesson" class="link">
                    Добавить урок
                </a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Schedule_Current_Lesson&amp;action=show"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Schedule_Current_Lesson&amp;action=show"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="schedule_current_lesson">
        <tr>
            <td><xsl:value-of select="id" /></td>

            <td>
                <xsl:value-of select="date" />
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
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Current_Lesson&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Current_Lesson" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>



</xsl:stylesheet>