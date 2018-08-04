<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-12">
            <h3>Выплаты преподавателям</h3>
            <table class="simple-little-table statistic_teacher_payments_table">
                <tr>
                    <td>Всего выплат на сумму:</td>
                    <td><xsl:value-of select="total_sum" /></td>
                </tr>
            </table>
        </div>
    </xsl:template>


</xsl:stylesheet>