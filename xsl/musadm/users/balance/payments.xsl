<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-payments section-bordered">
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
                <xsl:when test="type = 1 or type = 15">positive</xsl:when>
                <xsl:when test="type = 2">negative</xsl:when>
                <xsl:otherwise>neutral</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr class="{$class}" id="client_payment_{id}">
            <td class="date"><xsl:value-of select="datetime" /></td>
            <td class="value"><xsl:value-of select="value" /></td>

            <td>
                <p class="description">
                    <xsl:if test="status = 2">
                        <p class="text-danger">
                            Ошибка платежа
                        </p>
                    </xsl:if>
                    <xsl:if test="description != ''"><xsl:value-of select="description" /></xsl:if>
                </p>

                <span class="comments">
                    <input type="hidden" />
                    <xsl:if test="//is_admin = 1">
                        <xsl:for-each select="notes">
                            <xsl:if test="value != ''">
                                <p class="comment_{id}">
                                    <xsl:value-of select="value" />
                                </p>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:if>
                </span>
            </td>

            <xsl:if test="/root/is_admin = 1">
                <td style="text-align:center">
                    <a class="action comment" onclick="makePaymentCommentPopup({id}, savePaymentCommentClient)" title="Добавить комментарий"></a>
                    <xsl:if test="//access_payment_edit_client = 1">
                        <a class="action edit" onclick="makeClientPaymentPopup({id}, {user}, saveBalancePaymentCallback)" title="Редактирование платежа"></a>
                    </xsl:if>
                    <xsl:if test="//access_payment_delete_client = 1">
                        <a class="action delete" onclick="Payment.remove({id}, removeBalancePaymentCallback)" title="Удаление платежа"></a>
                    </xsl:if>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>