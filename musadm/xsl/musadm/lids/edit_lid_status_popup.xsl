<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <xsl:variable name="itemClass" select="lid_status/item_class" />
        <xsl:variable name="id" select="id" />

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Название</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid_status/title}" name="title" />
            </div>
            <hr/>

            <div class="column">
                <span>Цвет</span>
            </div>
            <div class="column">
                <select class="form-control" name="item_class" >
                    <xsl:for-each select="color">
                        <option value="{class}">
                            <xsl:if test="class = $itemClass">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <input type="hidden" name="id" value="{lid_status/id}" />
            <!--<input type="hidden" id="director" value="{user/id}" />-->

            <button class="lid_status_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>