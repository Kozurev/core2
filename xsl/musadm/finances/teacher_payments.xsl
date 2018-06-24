<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <h2>Выплаты</h2>

        <table class="table table-striped teacher_payments">
            <xsl:if test="user_group = 1 or user_group = 2">
                <tr class="header">
                    <td><input name="date" class="form-control" type="date" value="{date}"/></td>
                    <td><input name="summ" class="form-control" type="number" placeholder="Сумма" /></td>
                    <td>
                        <input type="hidden" name="userid" value="{userid}" />
                        <a class="btn btn-green add_teacher_payment">Добавить выплату</a>
                    </td>
                </tr>
                <tr>
                    <th>Дата</th>
                    <th colspan="2">Сумма</th>
                </tr>
            </xsl:if>

            <xsl:apply-templates select="month" />

        </table>


    </xsl:template>


    <xsl:template match="month">
        <tr>
            <td colspan="3">
                <xsl:value-of select="month_name" />
            </td>
        </tr>

        <xsl:for-each select="payment">
            <tr>
                <td><xsl:value-of select="datetime" /></td>
                <td colspan="2"><xsl:value-of select="value" /></td>
            </tr>
        </xsl:for-each>

        <!-- <xsl:apply-templates name="payment" /> -->
    </xsl:template>


    <xsl:template name="payment">
        <tr>
            <td><xsl:value-of select="datetime" /></td>
            <td colspan="2"><xsl:value-of select="value" /></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>