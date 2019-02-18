<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <html>
            <head>
                <meta http-equiv="content-type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
            </head>
            <body>
                <table>
                    <xsl:apply-templates select="user" />
                </table>
            </body>
        </html>
    </xsl:template>


    <xsl:template match="user">
        <tr>
            <td>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </td>
            <td>
                <xsl:value-of select="phone_number" />
                <xsl:apply-templates select="property_value[property_id = 16]" />
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="property_value">
        <br/><xsl:value-of select="value" />
    </xsl:template>


</xsl:stylesheet>