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

        <h3>Список платежей</h3>

        <div class="balance-payments">
            <table id="sortingTable" class="table">
                <thead>
                    <tr class="header">
                        <th>Дата</th>
                        <th>Сумма</th>
                        <xsl:if test="is_admin = 1">
                            <th>Примечание</th>
                            <th></th>
                        </xsl:if>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="payment" />
                </tbody>
            </table>
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

        <tr>
            <td class="{$class}"><xsl:value-of select="datetime" /></td>
            <td class="{$class}"><xsl:value-of select="value" /></td>

            <xsl:if test="//is_admin = 1">
                <td class="{$class}">
                    <xsl:if test="description != ''"><xsl:value-of select="description" /><br/></xsl:if>
                    <xsl:for-each select="notes">
                        <xsl:value-of select="value" /><br/>
                    </xsl:for-each>
                </td>
                <td class="{$class}" style="text-align:center">
                    <a class="btn btn-orange payment_add_note" data-modelid="{./id}">
                        Добавить примечание
                    </a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>