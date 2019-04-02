<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="certificate">
        <form name="createData" id="createData" action="." >
            <div class="column"><span>Дата продажи</span><span style="color:red">*</span></div>
            <div class="column"><input type="date" required="required" name="sellDate" class="form-control" value="{sell_date}" /></div>
            <div class="column"><span>Действителен до</span><span style="color:red">*</span></div>
            <div class="column"><input type="date" required="required" name="activeTo" class="form-control" value="{active_to}" /></div>
            <div class="column"><span>Номер</span><span style="color:red">*</span></div>
            <div class="column"><input type="text" required="required" name="number" class="form-control" value="{number}" /></div>
            <xsl:if test="/root/is_new = 1">
                <div class="column"><span>Примечание</span><span style="color:red">*</span></div>
                <div class="column"><textarea required="required" name="note" class="form-control"></textarea></div>
            </xsl:if>

            <input type="hidden" name="id" value="{id}" />
            <button class="popop_certificate_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>
</xsl:stylesheet>