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
                <span>Примечание (общее)</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <textarea class="form-control" name="description"></textarea>
            </div>

            <div class="column">
                <span>Примечание (для админа)</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <textarea class="form-control" name="property_26"></textarea>
            </div>

            <div class="column">
                <span>Тип операци</span>
            </div>
            <div class="column">
                <p style="margin-top: 5px">
                    <input type="radio" name="type" id="type1" value="1" style="height: auto" checked="checked" />
                    <!--<span>Зачисление</span>-->
                    <label for="type1">Зачисление</label>
                </p>
                <p style="margin-top: 5px">
                    <input type="radio" name="type" id="type2" value="2" style="height: auto"/>
                    <!--<span>Списание</span>-->
                    <label for="type2">Списание</label>
                </p>
            </div>

            <input type="hidden" id="payment_from" value="{function}" />
            <!--<input type="hidden" name="user" value="{user/id}" />-->

            <button class="popop_balance_payment_submit btn btn-default" data-userid="{user/id}">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>