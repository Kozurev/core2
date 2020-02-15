<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="root">
    <div class="col-lg-4">
        <!--<h3>Выплаты преподавателям</h3>-->
        <table class="table table-bordered table-hover statistic_teacher_payments_table" >
            <tr>
                <th colspan="2">Число активных клиетов</th>
            </tr>
            <tr>
                <td>Всего клиентов:</td>
                <td><xsl:value-of select="total_count" /> </td>
            </tr>
            <tr>
                <td>Из них стоят в расписании:</td>
                <td><xsl:value-of select="active_count" /> </td>
            </tr>
        </table>
    </div>
</xsl:template>


</xsl:stylesheet>