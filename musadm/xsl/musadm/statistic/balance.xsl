<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-4">
            <h3>Баланс и кол-во занятий</h3>
            <table class="table table-bordered statistic_lessons_table">
                <tr class="header">
                    <td>Общий баланс средств всех учащихся:</td>
                    <td><xsl:value-of select="balance" /> руб.</td>
                </tr>
                <tr>
                    <td>Кол-во оплаченных индивидуальных уроков:</td>
                    <td><xsl:value-of select="indiv_pos" /></td>
                </tr>
                <tr>
                    <td>Кол-во оплаченных групповых уроков:</td>
                    <td><xsl:value-of select="group_pos" /></td>
                </tr>
                <tr>
                    <td>Кол-во неоплаченных индивидуальных уроков:</td>
                    <td><xsl:value-of select="indiv_neg" /></td>
                </tr>
                <tr>
                    <td>Кол-во неоплаченных групповых уроков:</td>
                    <td><xsl:value-of select="group_neg" /></td>
                </tr>
            </table>
        </div>
    </xsl:template>


</xsl:stylesheet>