<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="section-bordered">
            <h3 id="show-client-filter">
                <span>Фильтры </span>
                <i class="fa fa-chevron-down"><input type="hidden" name="kostul"/></i>
            </h3>

            <form method="GET" action="{wwwroot}{action}" id="client-filter">
                <div class="row client-filter__options">
                    <div class="col-lg-3 dropdown-parent">
                        <h4>Филиалы</h4>
                        <div class="row">
                            <ul class="list">
                                <xsl:apply-templates select="schedule_area" />
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <h4>Инструмент</h4>
                        <div class="row">
                            <ul class="list">
                                <xsl:apply-templates select="property_value[property_id = 20]" />
                            </ul>
                            <span style="display:none">1</span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <h4>Преподаватели</h4>
                        <div class="row">
                            <ul class="list">
                                <xsl:apply-templates select="property_value[property_id = 21]" />
                            </ul>
                            <span style="display:none">1</span>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <h4>Сменный график</h4>
                        <div class="row">
                            <ul class="list">
                                <li>
                                    <input class="checkbox-new" type="checkbox" name="property_32[]" id="property_32_1" value="1" />
                                    <label class="label-new" for="property_32_1">
                                        <div class="tick"><span style="display:none">1</span></div>
                                    </label>
                                    <span>Да</span>
                                </li>
                                <li>
                                    <input class="checkbox-new" type="checkbox" name="property_32[]" id="property_32_2" value="0" />
                                    <label class="label-new" for="property_32_2">
                                        <div class="tick"><span style="display:none">1</span></div>
                                    </label>
                                    <span>Нет</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row right">
                    <input type="submit" class="btn btn-primary" value="Применить" />
                </div>
            </form>
        </section>
    </xsl:template>


    <xsl:template match="schedule_area">
        <li>
            <input class="checkbox-new" type="checkbox" name="areas[]" id="area_{id}" value="{id}" />
            <label class="label-new" for="area_{id}">
                <div class="tick"><span style="display:none">1</span></div>
            </label>
            <span><xsl:value-of select="title" /></span>
        </li>
    </xsl:template>


    <xsl:template match="property_value">
        <li>
            <input class="checkbox-new" type="checkbox" name="property_{property_id}[]" id="property_{id}" value="{id}" />
            <label class="label-new" for="property_{id}">
                <div class="tick"><span style="display:none">1</span></div>
            </label>
            <span><xsl:value-of select="value" /></span>
        </li>
    </xsl:template>


</xsl:stylesheet>