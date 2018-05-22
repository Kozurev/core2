<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <h1>Выплаты</h1>
        <!--<table id="sortingTable" class="tablesorter">-->
            <!--<thead>-->
                <!--<tr>-->
                    <!--<th class="header">Дата</th>-->
                    <!--<th class="header">Сумма</th>-->
                <!--</tr>-->
            <!--</thead>-->

            <!--<tbody>-->
                <!--<xsl:apply-templates select="payment" />-->
            <!--</tbody>-->
        <!--</table>-->

        <table class="table teacher_payments">
            <tr>
                <td><input name="date" class="form-control" type="date" value="{date}"/></td>
                <td><input name="summ" class="form-control" type="number" placeholder="Сумма" /></td>
                <td>
                    <input type="hidden" name="userid" value="{userid}" />
                    <button class="btn btn-success add_teacher_payment">Добавить выплату</button>
                </td>
            </tr>
            <tr>
                <th colspan="2">Дата</th>
                <th>Сумма</th>
            </tr>
            <xsl:apply-templates select="payment" />
        </table>
    </xsl:template>


    <xsl:template match="payment">
        <tr>
            <td colspan="2"><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="value" /></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>