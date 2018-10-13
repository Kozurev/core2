<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <!--<style>-->
            <!--.positive {-->
            <!--background-color:palegreen !important;-->
            <!--}-->
            <!--.negative {-->
            <!--background-color:indianred !important;-->
            <!--}-->
            <!--.neutral {-->
            <!--background-color:lightyellow !important;-->
            <!--}-->
        <!--</style>-->

        <h3>Список платежей</h3>

        <div class="balance-payments tab">
            <table id="sortingTable" class="table">
                <thead>
                    <tr class="header">
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Примечание</th>
                        <xsl:if test="is_admin = 1">
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
                <xsl:when test="type = 1">positive</xsl:when>
                <xsl:when test="type = 2">negative</xsl:when>
                <xsl:otherwise>neutral</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr>
            <td class="{$class}"><xsl:value-of select="datetime" /></td>
            <td class="{$class}"><xsl:value-of select="value" /></td>

            <td class="{$class}">
                <xsl:if test="description != ''"><xsl:value-of select="description" /></xsl:if>

                <xsl:if test="//is_admin = 1">
                    <xsl:for-each select="notes">
                        <br/><xsl:value-of select="value" />
                    </xsl:for-each>
                </xsl:if>
            </td>

            <xsl:if test="//is_admin = 1">
                <td class="{$class}" style="text-align:center">
                    <a class="btn btn-orange payment_add_note" data-modelid="{./id}">+</a>
                    <xsl:if test="//parent_user/group_id = 1 or //parent_user/group_id = 6">
                        <a class="action edit payment_edit" href="#" data-id="{id}"></a>
                        <a class="action delete payment_delete" href="#" data-id="{id}"></a>
                    </xsl:if>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>