<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class='row'>
<!--            <div class='col-lg-4'>-->
                <table class="table table-hover table-bordered">
                    <tr>
                        <th colspan="2">Доходы-расходы</th>
                    </tr>
                    <tr>
                        <td>Поступления за период:</td>
                        <td><xsl:value-of select="deposits" /></td>
                    </tr>
                    <tr>
                        <td>Возвраты за период:</td>
                        <td><xsl:value-of select="refunds" /></td>
                    </tr>
                    <tr>
                        <td>Выручка от занятий</td>
                        <td><xsl:value-of select="income" /></td>
                    </tr>
                    <tr>
                        <td>Начислено преподавателям</td>
                        <td><xsl:value-of select="expenses" /></td>
                    </tr>
                    <tr>
                        <td>Выручка с учетом отчислений преподавателям</td>
                        <td><xsl:value-of select="profit" /></td>
                    </tr>
                    <tr>
                        <td>Начислено клиентам кэшбека и бонусов</td>
                        <td><xsl:value-of select="bonuses" /></td>
                    </tr>
                    <tr>
                        <td>Хозрасходы</td>
                        <td><xsl:value-of select="host_expenses" /></td>
                    </tr>
                    <tr>
                        <td>Прибыль с учетом зарплат и хозрасходов</td>
                        <td><xsl:value-of select="profit - host_expenses" /></td>
                    </tr>
                    <tr>
                        <td>Выручка от частных занятий</td>
                        <td><xsl:value-of select="income2" /></td>
                    </tr>
                </table>
            </div>
<!--        </div>-->
    </xsl:template>

</xsl:stylesheet>