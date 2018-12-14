<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        text:   { required: true },
                    },
                    messages: {
                        text:   { required: "Это поле обязательноое к заполнению" },
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Текст задачи</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="text" name="text" class="form-control" />
            </div>

            <div class="date">
                <div class="column">
                    <span>Дата контроля</span>
                </div>
                <div class="column">
                    <input type="date" name="date" class="form-control" value="{date}" />
                </div>
            </div>

            <div class="column">
                <span>Клиент</span>
            </div>
            <div class="column">
                <select name="associate" class="form-control">
                    <option value="0"> ... </option>
                    <xsl:for-each select="user">
                        <option value="{id}">
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>


            <!--<input type="hidden" name="user" value="{user/id}" />-->

            <button class="popop_task_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>