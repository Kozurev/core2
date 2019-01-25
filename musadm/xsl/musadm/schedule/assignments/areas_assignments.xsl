<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="areas">
        <span data-area-id="{id}"><xsl:if test="position() != 1"><br/></xsl:if>
            <xsl:value-of select="title" /></span>
    </xsl:template>

</xsl:stylesheet>