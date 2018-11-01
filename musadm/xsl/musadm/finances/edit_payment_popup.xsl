<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Сумма</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <input type="number" name="summ" class="form-control" value="{payment/value}" />
            </div>
            <div class="column">
                <span>Дата</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <input type="date" name="date" class="form-control" value="{payment/datetime}" />
            </div>
            <div class="column">
                <span>Примечание</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <textarea name="description" class="form-control"><xsl:value-of select="payment/description" /></textarea>
            </div>

            <input type="hidden" name="id" value="{payment/id}" />
            <input type="hidden" name="after_save_action" value="{afterSaveAction}" />
            <button class="popop_payment_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>