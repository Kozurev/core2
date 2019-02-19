<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-payments">
            <h3>Список платежей</h3>

            <xsl:choose>
                <xsl:when test="count(payment) = 0">
                    <p>Финансовых операций не найдено</p>
                </xsl:when>
                <xsl:otherwise>
                    <div class="balance-payments tab">
                        <table id="sortingTable" class="table table-statused">
                            <thead>
                                <tr class="header">
                                    <th>Дата</th>
                                    <th>Сумма</th>
                                    <th>Примечание</th>
                                    <xsl:if test="is_admin = 1">
                                        <th><!--Костыль--></th>
                                    </xsl:if>
                                </tr>
                            </thead>

                            <tbody>
                                <xsl:apply-templates select="payment" />
                            </tbody>
                        </table>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </section>
    </xsl:template>


    <xsl:template match="payment">
        <xsl:variable name="class">
            <xsl:choose>
                <xsl:when test="type = 1">positive</xsl:when>
                <xsl:when test="type = 2">negative</xsl:when>
                <xsl:otherwise>neutral</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr class="{$class}">
            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="value" /></td>

            <td>
                <xsl:if test="description != ''"><xsl:value-of select="description" /></xsl:if>

                <xsl:if test="//is_admin = 1">
                    <xsl:for-each select="notes">
                        <br/><xsl:value-of select="value" />
                    </xsl:for-each>
                </xsl:if>
            </td>

            <xsl:if test="//is_admin = 1">
                <td style="text-align:center">
                    <a class="action comment payment_add_note" data-modelid="{./id}" title="Добавить комментарий"></a>
                    <xsl:if test="//parent_user/group_id = 1 or //parent_user/group_id = 6">
                        <a class="action edit payment_edit" href="#" data-id="{id}" data-after_save_action="client" title="Редактирование платежа"></a>
                        <a class="action delete payment_delete" href="#" data-id="{id}" data-after_save_action="client" title="Удаление платежа"></a>
                    </xsl:if>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>