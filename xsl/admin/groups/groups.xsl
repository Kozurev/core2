<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <xsl:if test="parent_id = 0">
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Название</th>
                    <th>Длительность</th>
                    <th>Действия</th>
                    <!--<th>Редактирование</th>-->
                    <!--<th>Удаление</th>-->
                </tr>
                <xsl:apply-templates select="schedule_group" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Group&amp;parent_id={parent_id}" class="link">
                    Создать группу
                </a>
            </button>
            </xsl:if>

            <xsl:if test="parent_id != 0">
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>ФИО</th>
                    <th>Действия</th>
                </tr>
                <xsl:apply-templates select="schedule_group_assignment" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Group_Assignment&amp;parent_id={parent_id}" class="link">
                    Добавить пользователя в группу
                </a>
            </button>
            </xsl:if>



            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Groups&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Groups&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="schedule_group">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td class="table_structure">
                <a class="link" href="admin?menuTab=Groups&amp;menuAction=show&amp;parent_id={id}">
                    <xsl:value-of select="title" />
                </a>
            </td>

            <td><xsl:value-of select="duration" /></td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Group&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Group" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="schedule_group_assignment">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="surname" />
            </td>
            <!--Удаление-->
            <td><a href="admin" data-model_name="Schedule_Group_Assignment" data-model_id="{id}" class="delete deleteLink"></a></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>