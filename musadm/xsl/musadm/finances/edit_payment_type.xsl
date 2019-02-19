<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div class="row popup-row-block">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="input_new_payment_type">Введите название:</label>
                <input type="text" id="input_new_payment_type" class="form-control" placeholder="Тип платежа" />

                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <a href="#" class="btn btn-large btn-green finances_payment_type_append">Сохранить</a>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <select class="form-control finances_payment_type_list" multiple="multiple" size="10">
                    <xsl:apply-templates select="type" />
                </select>

                <div class="row">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <a href="#" class="btn btn-red btn-large finances_payment_type_delete">Удалить</a>
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="type">
        <option value="{id}"><xsl:value-of select="title" /></option>
    </xsl:template>


</xsl:stylesheet>