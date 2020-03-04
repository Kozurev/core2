<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12 right">
                <h4>Филиалы</h4>
            </div>

            <div class="col-lg-10 col-md-8 col-sm-6 col-xs-12">
                <xsl:apply-templates select="schedule_area" />
            </div>
<!--            <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">-->
<!--                <select class="form-control" name="areas_ids[]" id="statistic-areas-select">-->
<!--                    <option value="0">Общая</option>-->
<!--                    <xsl:apply-templates select="schedule_area" />-->
<!--                </select>-->
<!--            </div>-->
        </div>
    </xsl:template>

    <xsl:template match="schedule_area">
<!--        <option value="{id}"><xsl:value-of select="title" /></option>-->
        <div style="display: inline-block">
            <input type="checkbox" value="{id}" name="statistic_area_ids[]" id="statistic_area_id_{id}" />
            <label for="statistic_area_id_{id}" style="margin-right: 25px; margin-left: 5px;">
                <xsl:value-of select="title" />
            </label>
        </div>
    </xsl:template>

</xsl:stylesheet>