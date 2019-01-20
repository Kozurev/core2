<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div class="row finances_popup_row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="input_new_payment_type">Введите название:</label>
                <input type="text" id="input_new_payment_type" class="form-control" placeholder="Тип платежа" />

                <a href="#" class="btn btn-green finances_payment_type_append">Сохранить</a>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <select class="form-control finances_payment_type_list" multiple="multiple" size="10">
                    <xsl:apply-templates select="type" />
                </select>

                <a href="#" class="btn btn-red finances_payment_type_delete">Удалить</a>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="type">
        <option value="{id}"><xsl:value-of select="title" /></option>
    </xsl:template>


</xsl:stylesheet>