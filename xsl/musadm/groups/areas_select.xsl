<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="areas_row">
        <xsl:variable name="currentArea" select="//current_area" />
        <div><h4>Филиалы</h4></div>
        <div>
            <select class="form-control" name="area_id">
                <option value="0">...</option>
                <xsl:for-each select="/root/schedule_area">
                    <xsl:variable name="area_id" select="id" />
                    <option value="{id}">
                        <xsl:if test="$area_id = $currentArea">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="title" />
                    </option>
                </xsl:for-each>
            </select>
        </div>
    </xsl:template>

</xsl:stylesheet>
