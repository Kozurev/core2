<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">
        <form name="createData" id="createData" action="." novalidate="novalidate">"
            <div class="column"><span>Сумма</span><span style="color:red">*</span></div>
            <div class="column"><input type="number" required="required" name="summ" class="form-control" /></div>
            <div class="column"><span>Примечание</span><span style="color:red">*</span></div>
            <div class="column"><textarea required="required" name="note" class="form-control"></textarea></div>

            <button class="popop_custom_payment_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>