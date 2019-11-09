<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="section-bordered">
            <form method="GET" action="{wwwroot}{action}" id="client-filter">
                <div class="row">
                    <div class="col-lg-1"><h4>Фамилия</h4></div>
                    <div class="col-lg-2">
                        <input type="text" name="surname" class="form-control" />
                    </div>
                    <div class="col-lg-1"><h4>Имя</h4></div>
                    <div class="col-lg-2">
                        <input type="text" name="name" class="form-control" />
                    </div>
                    <div class="col-lg-1"><h4>Телефон</h4></div>
                    <div class="col-lg-2">
                        <input type="text" name="phone_number" class="form-control" />
                    </div>
                    <div class="col-lg-2">
                        <input type="submit" class="btn btn-primary" value="Применить" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <h4 id="show-client-filter">
                            <span>Фильтры </span>
                            <i class="fa fa-chevron-down"><input type="hidden" name="kostul"/></i>
                        </h4>
                    </div>
                </div>

                <div class="client-filter__options">
                    <div class="row">
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
                            <h4>Поурочно</h4>
                            <div class="row">
                                <ul class="list">
                                    <li>
                                        <input type="checkbox" name="property_32[]" id="property_32_1" value="1" />
                                        <label for="property_32_1">Да</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" name="property_32[]" id="property_32_2" value="0" />
                                        <label for="property_32_2">Нет</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="active" value="{usersActive}" />

                <!--<div class="row right">-->
                    <!--<input type="submit" class="btn btn-primary" value="Применить" />-->
                <!--</div>-->
            </form>
        </section>
    </xsl:template>


    <xsl:template match="schedule_area">
        <li>
            <!--<input class="checkbox-new" type="checkbox" name="areas[]" id="area_{id}" value="{id}" />-->
            <!--<label class="label-new" for="area_{id}">-->
                <!--<div class="tick"><span style="display:none">1</span></div>-->
            <!--</label>-->
            <!--<span><xsl:value-of select="title" /></span>-->
            <input type="checkbox" name="areas[]" id="area_{id}" value="{id}" />
            <label for="area_{id}">
                <xsl:value-of select="title" />
            </label>
        </li>
    </xsl:template>


    <xsl:template match="property_value">
        <li>
            <!--<input class="checkbox-new" type="checkbox" name="property_{property_id}[]" id="property_{id}" value="{id}" />-->
            <!--<label class="label-new" for="property_{id}">-->
                <!--<div class="tick"><span style="display:none">1</span></div>-->
            <!--</label>-->
            <!--<span><xsl:value-of select="value" /></span>-->
            <input type="checkbox" name="property_{property_id}[]" id="property_{id}" value="{id}" />
            <label for="property_{id}">
                <xsl:value-of select="value" />
            </label>
        </li>
    </xsl:template>


</xsl:stylesheet>