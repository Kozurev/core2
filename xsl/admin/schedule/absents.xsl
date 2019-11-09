<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Периоды отсутствия</h3>


            <table class="table">
                <th>id</th>
                <th>Ученик</th>
                <th>Период "С"</th>
                <th>Период "По"</th>
                <th>Действия</th>
                <xsl:apply-templates select="schedule_absent" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Absent&amp;menuAction=updateForm&amp;model=Schedule_Absent" class="link">
                    Добавить период
                </a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Absent&amp;action=show"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Absent&amp;action=show"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="schedule_absent">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="type_id = 1">
                        <xsl:value-of select="client/surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="client/name" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="client/title" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td><xsl:value-of select="date_from" /></td>
            <td><xsl:value-of select="date_to" /></td>
            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Absent&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Absent" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>