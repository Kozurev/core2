<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
        <!--<h3>Лиды</h3>-->
            <table class="table table-bordered table-hover statistic_lids_table">
                <tr>
                    <th colspan="2">Лиды</th>
                </tr>
                <tr>
                    <td>Всего:</td>
                    <td><xsl:value-of select="total" /></td>
                </tr>
                <xsl:apply-templates select="status" />
            </table>
        </div>
    </xsl:template>


    <xsl:template match="status">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td>
                <xsl:value-of select="count" /> (<xsl:value-of select="percents" />%)
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>