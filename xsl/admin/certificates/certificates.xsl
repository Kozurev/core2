<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Сертификаты</h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Дата продажи</th>
                    <th>Активен до</th>
                    <th>Номер</th>
                    <th>Примечание</th>
                    <th>Действия</th>
                </tr>
                <xsl:apply-templates select="certificate" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Certificate" class="link">
                    Добавить сертификат
                </a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Certificate&amp;action=show"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Certificate&amp;action=show"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="certificate">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="sell_date" /></td>
            <td><xsl:value-of select="active_to" /></td>
            <td><xsl:value-of select="number" /></td>
            <td><xsl:value-of select="note" /></td>
            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Certificate&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Certificate" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>