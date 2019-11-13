<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="areas">
        <span data-area-id="{id}">
        	<!-- <xsl:if test="position() != 1"><br/></xsl:if> -->
            <xsl:value-of select="title" />
            <xsl:if test="position() != last()"><br/></xsl:if>
        </span>
    </xsl:template>

    <xsl:template match="schedule_area_assignment">
    	<xsl:variable name="areaId" select="area_id" />
        <span data-area-id="{$areaId}">
        	<!-- <xsl:if test="position() != 1"><br/></xsl:if> -->
            <xsl:value-of select="//schedule_area[id = $areaId]/title" />
            <xsl:if test="position() != last()"><br/></xsl:if>
        </span>
    </xsl:template>

</xsl:stylesheet>