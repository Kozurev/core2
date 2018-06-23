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

        <table id="sortingTable" class="table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Примечание</th>
                    <xsl:if test="user_group/id != 5">
                        <th></th>
                    </xsl:if>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="payment" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="payment">

        <xsl:variable name="class">
            <xsl:choose>
                <xsl:when test="type = 1 and value &gt; 0">positive</xsl:when>
                <xsl:when test="type = 0 and value &gt; 0">negative</xsl:when>
                <xsl:otherwise>neutral</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr>
            <td class="{$class}"><xsl:value-of select="datetime" /></td>
            <td class="{$class}"><xsl:value-of select="value" /></td>
            <!--<td class="{$class}"><xsl:value-of select="description" /></td>-->
            <td class="{$class}">
                <xsl:value-of select="description" />
                <xsl:for-each select="notes">
                    <br/><xsl:value-of select="value" />
                </xsl:for-each>
            </td>
            <xsl:if test="//user_group/id != 5">
                <td class="{$class}" style="text-align:center">
                    <a class="btn btn-orange payment_add_note" data-modelid="{./id}">
                        Добавить примечание
                    </a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>