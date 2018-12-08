<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <hr/>
        <div class="row client-notes">
            <div class="col-lg-6 col-md-6 col-sm-12" >
                <input class="form-control" placeholder="Заметки" value="{note/value}" id="client_notes" data-userid="{note/object_id}" />
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2">
                <span>Поурочно: </span>

                <input type="checkbox" id="per_lesson" class="checkbox-new" data-userid="{note/object_id}" >
                    <xsl:if test="per_lesson/value = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="per_lesson" class="label-new" style="position: relative; top: 5px;">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
            </div>

            <xsl:if test="entry/value != ''">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <span>Последний раз онлайн: <xsl:value-of select="entry/value" /></span>
                </div>
            </xsl:if>
        </div>

        <hr/>
    </xsl:template>

</xsl:stylesheet>