<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section id="myCalls_section">
            <div class="row">
                <div class="col-md-4 col-sm-9">
                    <input class="form-control" id="myCalls_api_url" value="{api_url}" />
                </div>
                <div class="col-md-2 col-sm-3">
                    <a class="action save" id="myCalls_api_url_save"><input type="hidden" /></a>
                </div>
                <input type="hidden" id="director_id" value="{director/id}" />
            </div>
        </section>
    </xsl:template>

</xsl:stylesheet>