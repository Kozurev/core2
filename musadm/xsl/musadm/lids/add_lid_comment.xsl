<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">

            <div class="column">
                <span>Комментарий</span>
            </div>
            <div class="column">
                <textarea name="text"></textarea>
            </div>

            <input type="hidden" name="lidId" value="{lid/id}" />
            <input type="hidden" name="id" value="0" />
            <input type="hidden" name="modelName" value="Lid_Comment" />

            <button class="popop_lid_comment_submit btn btn-default" data-userid="{payment/user}">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>