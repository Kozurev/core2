<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        //text:   { required: true },
                    },
                    messages: {
                        //text:   { required: "Это поле обязательноое к заполнению" },
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Название</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="text" name="title" class="form-control" value="{payment_tarif/title}" />
            </div>

            <div class="column">
                <span>Цена</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="number" name="price" class="form-control" value="{payment_tarif/price}" />
            </div>

            <div class="column">
                <span>Публичность</span>
            </div>
            <div class="column right">
                <input type="checkbox" name="access" id="access" class="checkbox-new" >
                    <xsl:if test="payment_tarif/access = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="access" class="label-new">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
            </div>

            <div class="column">
                <span>Кол-во индивидуальных уроков</span>
            </div>
            <div class="column">
                <input type="number" name="countIndiv" class="form-control" value="{payment_tarif/count_indiv}" />
            </div>

            <div class="column">
                <span>Кол-во групповых уроков</span>
            </div>
            <div class="column">
                <input type="number" name="countGroup" class="form-control" value="{payment_tarif/count_group}"/>
            </div>

            <input type="hidden" name="modelName" value="Payment_Tarif" />
            <input type="hidden" name="id" value="{payment_tarif/id}" />

            <button class="popop_tariff_submit btn btn-default">Сохранить</button>
        </form>


    </xsl:template>

</xsl:stylesheet>