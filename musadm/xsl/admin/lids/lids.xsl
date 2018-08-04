<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <xsl:if test="parent_id = 0">
            <table class="table">
                <th>id</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>ВК</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
                <xsl:apply-templates select="lid" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Lid&amp;properties[]=27" class="link">
                    Добавить лида
                </a>
            </button>
            </xsl:if>

            <xsl:if test="parent_id != 0">
                <table class="table">
                    <th>id</th>
                    <th>Автор</th>
                    <th>Дата</th>
                    <th>Текст</th>
                    <th>Действия</th>
                    <xsl:apply-templates select="lid_comment" />
                </table>

                <button class="btn button" type="button">
                    <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Lid_Comment&amp;parent_id={parent_id}" class="link">
                        Добавить комментарий
                    </a>
                </button>
            </xsl:if>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Lid&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Lid&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="lid">
        <tr>
            <td><xsl:value-of select="id" /></td>

            <td class="table_structure">
                <a class="link" href="admin?menuTab=lid&amp;menuAction=show&amp;parent_id={id}">
                    <xsl:choose>
                        <xsl:when test="surname != '' or name != ''">
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </xsl:when>
                        <xsl:otherwise>
                            ----
                        </xsl:otherwise>
                    </xsl:choose>
                </a>
            </td>

            <td><xsl:value-of select="number" /></td>
            <td><xsl:value-of select="vk" /></td>
            <td><xsl:value-of select="control_date" /></td>
            <td><xsl:value-of select="status/value" /></td>


            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Lid&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Lid" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="lid_comment">
        <tr>
            <td><xsl:value-of select="id" /></td>

            <td>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </td>

            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="text" /></td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Lid_Comment&amp;model_id={id}&amp;parent_id={//parent_id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Lid_Comment" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>