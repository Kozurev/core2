<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">

            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Действия</th>
                    <!--<th>Редактировать</th>-->
                    <!--<th>Удалить</th>-->
                </tr>
                <xsl:apply-templates select="page_template_dir" />
                <xsl:apply-templates select="page_template" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Page_Template_Dir&amp;parent_id={parent_id}&amp;dir_id={dir_id}" class="link">
                    Создать диекторию
                </a>
            </button>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Page_Template&amp;parent_id={parent_id}&amp;dir_id={dir_id}" class="link">
                    Создать макет
                </a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Template&amp;action=show&amp;parent_id={parent_id}&amp;dir_id={dir_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Template&amp;action=show&amp;parent_id={parent_id}&amp;dir_id={dir_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="page_template">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <a class="link" href="admin?menuTab=Template&amp;menuAction=show&amp;parent_id={id}&amp;dir_id=0">
                    <xsl:value-of select="title" />
                </a>
            </td>

            <td></td>


            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Page_Template&amp;parent_id={parent_id}&amp;dir_id={//dir_id}&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Page_Template" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="page_template_dir">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <a class="link dir" href="admin?menuTab=Template&amp;menuAction=show&amp;parent_id=0&amp;dir_id={id}">
                    <xsl:value-of select="title" />
                </a>
            </td>

            <td></td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Page_Template_Dir&amp;parent_id=0&amp;dir_id={id}&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Page_Template_Dir" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>