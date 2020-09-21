<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="areas_row">
        <xsl:variable name="currentArea" select="//current_area = 5" />
        <div><h4>Филиалы</h4></div>
        <div>
            <select class="form-control" name="area_id"
                    onchange="showFinancesHistory(
                        $('input[name=date_from]').val(),
                        $('input[name=date_to]').val(),
                        this.value
                    )">
                <option value="0">...</option>
                <xsl:for-each select="schedule_area">
                    <xsl:variable name="areaId" select="id" />
                    <option value="{id}">
                        <xsl:if test="$areaId = $currentArea">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="title" />
                    </option>
                </xsl:for-each>
            </select>
        </div>
    </xsl:template>

</xsl:stylesheet>
