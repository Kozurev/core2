<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section id="myCalls_section">
            <div class="row">
                <div class="col-md-4 col-sm-9">
                    <input class="form-control" id="my_calls_url" value="{api_url}" />
                </div>
                <div class="col-md-2 col-sm-3">
                    <a class="action save property_value_save" data-property-name="my_calls_url" data-model-name="User" data-object-id="{director/id}"><input type="hidden" /></a>
                </div>
                <!--<input type="hidden" id="director_id" value="{director/id}" />-->
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-9">
                    <input class="form-control" id="my_calls_token" value="{api_token}" />
                </div>
                <div class="col-md-2 col-sm-3">
                    <a class="action save property_value_save" data-property-name="my_calls_token" data-model-name="User" data-object-id="{director/id}"><input type="hidden" /></a>
                </div>
                <!--<input type="hidden" id="director_id" value="{director/id}" />-->
            </div>
        </section>
    </xsl:template>

</xsl:stylesheet>