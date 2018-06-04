<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <h3>Лиды</h3>
            <table class="simple-little-table statistic_lids_table">
                <tr>
                    <th>Всего:</th>
                    <th><xsl:value-of select="total" /></th>
                </tr>
                <xsl:apply-templates select="status" />
            </table>
        </div>
    </xsl:template>


    <xsl:template match="status">
        <tr>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="percents" />%</td>
        </tr>
    </xsl:template>

</xsl:stylesheet>