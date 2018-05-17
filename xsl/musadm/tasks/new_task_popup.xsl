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

            <div class="column">
                <span>Тир задачи</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <select name="type" class="form-control">
                    <xsl:for-each select="task_type">
                        <option value="{id}"><xsl:value-of select="title" /></option>
                    </xsl:for-each>
                </select>
            </div>

            <div class="date" style="display:none">
                <div class="column">
                    <span>Дата контроля</span>
                </div>
                <div class="column">
                    <input type="date" name="date" class="form-control" value="{date}" />
                </div>
            </div>

            <!--<input type="hidden" name="user" value="{user/id}" />-->

            <button class="popop_task_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>