<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class='row'>
            <div class='col-lg-12 col-md-12 col-sm-12'>
                <table class="table table-bordered">
                    <tr class="header">
                        <td colspan="2">Доходы-расходы</td>
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
                        <td>Хозрасходы</td>
                        <td><xsl:value-of select="host_expenses" /></td>
                    </tr>
                    <tr>
                        <td>Прибыль с учетом зарплат и хозрасходов</td>
                        <td><xsl:value-of select="profit - host_expenses" /></td>
                    </tr>
                </table>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>