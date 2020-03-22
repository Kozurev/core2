<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section id="jivo_section">
            <div class="row">
                <div class="col-md-12">
                    <h4>Укажите активность чата и скрипт, который предоставил Jivo</h4>
                </div>
                <div class="col-md-2 col-md-offset-1">
                    <input class="checkbox property_value_checkbox" id="jivo_active" type="checkbox" data-property-name="jivo_active" data-model-name="User" data-object-id="{director/id}">
                        <xsl:if test="jivo_active = 1">
                            <xsl:attribute name="checked">true</xsl:attribute>
                        </xsl:if>
                    </input>
                    <label for="jivo_active" class="checkbox-label">
                        <span class="off">Отключен</span>
                        <span class="on">Активен</span>
                    </label>
                </div>
                <div class="col-md-6 col-sm-9">
                    <input class="form-control" id="jivo_script" value="{jivo_script}" />
                </div>
                <div class="col-md-2 col-sm-3">
                    <a class="action save property_value_save" data-property-name="jivo_script" data-model-name="User" data-object-id="{director/id}"><input type="hidden" /></a>
                </div>
                <!--<input type="hidden" id="director_id" value="{director/id}" />-->
            </div>
        </section>
    </xsl:template>

</xsl:stylesheet>