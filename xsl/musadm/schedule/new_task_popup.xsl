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

            <button class="popop_schedule_task_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>