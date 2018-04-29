<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <input class="form-control" placeholder="Заметки" value="{note/value}" id="client_notes" data-userid="{note/object_id}"
               style="width: 60%;display:inline-block"/>
        <xsl:if test="entry/value != ''">
            <span style="width: 30%;float:right;font-size:16px;margin-top:6px;">
                Последний раз онлайн: <xsl:value-of select="entry/value" />
            </span>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>