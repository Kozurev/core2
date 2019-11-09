<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row popup-row-block areas-popup">
            <div class="col-md-5">
                <h4>Список филиалов:</h4>
                <select id="areas-list" class="form-control" multiple="multiple" size="6">
                    <xsl:apply-templates select="areas" />
                </select>
            </div>

            <div class="col-md-2 buttons">
                <a href="#" class="action arrow-right" id="append_assignment"></a>
            </div>

            <div class="col-md-5">
                <h4>Связь:</h4>
                <select id="areas-assignments" class="form-control" multiple="multiple" size="6">
                    <xsl:for-each select="assignments">
                        <xsl:variable name="areaId" select="area_id" />
                        <!--<option value="{id}" data-area-id="{$areaId}">-->
                        <option value="{$areaId}">
                            <xsl:call-template name="get-area-name" >
                                <xsl:with-param name="areaId" select="$areaId" />
                            </xsl:call-template>
                        </option>
                    </xsl:for-each>
                </select>

                <a class="btn btn-red btn-large" id="area_assignment_delete">Удалить</a>
            </div>

            <input type="hidden" id="model-id" value="{model-id}" />
            <input type="hidden" id="model-name" value="{model-name}" />
        </div>
    </xsl:template>


    <xsl:template match="areas">
        <option value="{id}">
            <xsl:value-of select="title" />
        </option>
    </xsl:template>


    <xsl:template name="get-area-name">
        <xsl:param name="areaId" />

        <xsl:value-of select="//areas[id = $areaId]/title" />
    </xsl:template>


</xsl:stylesheet>