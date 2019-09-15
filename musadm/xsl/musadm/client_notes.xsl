<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <hr/>
        <div class="row client-notes">
            <div class="col-lg-4 col-md-6 col-sm-6" >
                <textarea class="form-control" placeholder="Заметки" id="client_notes" data-userid="{note/object_id}" >
                    <xsl:choose>
                        <xsl:when test="note/value != ''">
                            <xsl:value-of select="note/value" />
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:text>&#x0A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </textarea>
            </div>

            <div class="col-lg-2 col-md-3 col-sm-6">
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

            <xsl:if test="absent/id != ''">
                <div class="col-lg-2 col-md-2 col-sm-6">
                    <span>Отсутствует:</span>
                    <br/>
                    <span id="absent-from"><xsl:value-of select="absent/date_from" /></span>
                    <span> - </span>
                    <span id="absent-to"><xsl:value-of select="absent/date_to" /></span>
                    <a class="action edit"><xsl:text>&#x0A;</xsl:text></a>
                    <a class="action delete"><xsl:text>&#x0A;</xsl:text></a>
                </div>
            </xsl:if>

            <xsl:if test="entry/value != ''">
                <div class="col-lg-4 col-md-3 col-sm-6">
                    <span>Последяя авторизация: <xsl:value-of select="entry/value" /></span>
                </div>
            </xsl:if>
        </div>

        <hr/>
    </xsl:template>

</xsl:stylesheet>