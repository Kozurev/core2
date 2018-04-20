<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <style>
            .positive {
            background-color:palegreen !important;
            }
            .negative {
            background-color:indianred !important;
            }
            .neutral {
            background-color:lightyellow !important;
            }
        </style>

        <div class="in_main">
            <h3 class="main_title">Платежи</h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>ФИО</th>
                    <th>Тип</th>
                    <th>Сумма</th>
                    <th>Дата</th>
                    <th>Примечание</th>
                </tr>
                <xsl:apply-templates select="payment" />
            </table>

            <button class="btn button" type="button" style="visibility:hidden">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Structure&amp;parent_id={parent_id}&amp;parent_name=Structure" class="link">
                    Новый раздел
                </a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Payment&amp;action=show"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Payment&amp;action=show"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="payment">

        <xsl:variable name="class">
            <xsl:choose>
                <xsl:when test="type = 1 and value &gt; 0">positive</xsl:when>
                <xsl:when test="type = 0 and value &gt; 0">negative</xsl:when>
                <xsl:otherwise>neutral</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr class="{$class}">
            <td><xsl:value-of select="id" /></td>
            <td>
                <xsl:value-of select="surname" />
                <xsl:text>  </xsl:text>
                <xsl:value-of select="name" />
            </td>
            <td>
                <xsl:if test="type = 0">Списание</xsl:if>
                <xsl:if test="type = 1">Зачисление</xsl:if>
            </td>
            <td><xsl:value-of select="value" /></td>
            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="description" /></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>