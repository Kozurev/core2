<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">

            <div class="column">
                <span>Примечание</span>
            </div>
            <div class="column">
                <textarea name="property_26[]"></textarea>
            </div>

            <xsl:for-each select="notes">
                <input type="hidden" name="property_26[]" value="{value}" />
            </xsl:for-each>

            <input type="hidden" name="id" value="{payment/id}" />
            <input type="hidden" name="modelName" value="Payment" />

            <button class="popop_payment_note_submit btn btn-default" data-userid="{payment/user}">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>