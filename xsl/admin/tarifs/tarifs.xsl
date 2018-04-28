<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                Тарифы
            </h3>

            <table class="table">
                <th>id</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Кол-во уроков</th>
                <th>Тип уроков</th>
                <th>Доступ</th>
                <th>Действия</th>
                <xsl:apply-templates select="payment_tarif" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Payment_Tarif" class="link">
                    Добавить тариф
                </a>
            </button>


            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Tarif&amp;action=show"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Tarif&amp;action=show"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="payment_tarif">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>


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

            <!--Редактирование-->
            <td><a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Lid&amp;model_id={id}" class="link updateLink" /></td>

            <!--Удаление-->
            <td><a href="admin" data-model_name="Lid_Comment" data-model_id="{id}" class="delete deleteLink"></a></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>