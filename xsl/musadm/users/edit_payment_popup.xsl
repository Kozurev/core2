<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        value:   { required: true },
                        description: { required: true },
                    },
                    messages: {
                        value:   { required: "Это поле обязательноое к заполнению" },
                        description: { required: "Это поле обязательноое к заполнению" }
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Сумма</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="number" name="value" class="form-control" />
            </div>

            <div class="column">
                <span>Примечание</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <textarea class="form-control" name="description"></textarea>
            </div>

            <div class="column">
                <span>Тип операци</span>
            </div>
            <div class="column">
                <p><input type="radio" name="type" value="1" style="height: auto" checked="checked" /><span>Зачисление</span></p>
                <p><input type="radio" name="type" value="0" style="height: auto"/><span>Списание</span></p>
            </div>

            <!--<input type="hidden" name="user" value="{user/id}" />-->

            <button class="popop_user_payment_submit btn btn-default" data-userid="{user/id}">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>