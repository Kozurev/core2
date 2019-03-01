<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row popup-row-block">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <label for="property_list_value">Значение:</label>
                <input type="text" id="property_list_value" class="form-control" placeholder="Новое значение" />

                <div class="row center">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <a href="#" class="btn btn-green btn-large" data-id="" id="property_list_save" data-prop-id="{property/id}">Сохранить</a>
                    </div>

                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 btn-cancel-block">
                        <a href="#" class="btn btn-purple btn-large" id="property_list_cancel">Отмена</a>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <select class="form-control" id="property_list_select" multiple="multiple" size="10">
                    <xsl:apply-templates select="lid_status" />
                </select>

                <div class="row center">
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <a href="#" class="btn btn-orange btn-large" id="property_list_edit">Изменить</a>
                    </div>
                    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
                        <a href="#" class="btn btn-red btn-large" id="property_list_delete">Удалить</a>
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="lid_status">
        <option value="{id}">
            <xsl:value-of select="title" />
        </option>
    </xsl:template>


</xsl:stylesheet>