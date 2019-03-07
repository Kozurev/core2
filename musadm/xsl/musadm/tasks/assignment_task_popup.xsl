<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">

            <div class="column">
                <span>Клиент</span>
            </div>
            <div class="column">
                <select name="associate" class="form-control">
                    <option value="0"> ... </option>
                    <xsl:for-each select="user">
                        <option value="{id}">
                            <xsl:if test="//task/associate = id">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>

            <input type="hidden" name="id" value="{task/id}" />
            <input type="hidden" name="modelName" value="Task" />

            <button class="btn btn-default" onclick="loaderOn(); saveData('Main', taskAfterAction)">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>