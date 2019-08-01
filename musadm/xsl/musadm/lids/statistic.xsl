<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:xsk="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <xsl:for-each select="table">
            <section>
                <h3>Статистика по <xsl:value-of select="table-title" /></h3>
                <table class="table table-hover table-striped table-bordered sortingTable">
                    <thead>
                        <tr>
                            <th><xsl:value-of select="title" /></th>
                            <th class="right">Всего</th>
                            <xsl:for-each select="/root/lid_status">
                                <th class="center"><xsl:value-of select="title" /></th>
                            </xsl:for-each>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="val" />
                    </tbody>
                </table>
            </section>
        </xsl:for-each>
    </xsl:template>


    <xsl:template match="val">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td class="right"><xsl:value-of select="total_count" /></td>
            <xsl:apply-templates select="status" />
        </tr>
    </xsl:template>


    <xsl:template match="status">
        <td class="center"><xsl:value-of select="count_lids" /></td>
    </xsl:template>

</xsl:stylesheet>