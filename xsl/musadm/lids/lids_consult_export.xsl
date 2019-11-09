<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="root">
        <html>
            <head>
                <meta http-equiv="content-type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
            </head>
            <body>
                <table>
                    <xsl:apply-templates select="lid" />
                </table>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="lid">
        <tr>
            <td>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </td>
            <td>
                <xsl:value-of select="number" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>