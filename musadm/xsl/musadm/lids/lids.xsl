<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="areas_select.xsl"/>
    <xsl:include href="lid_card.xsl" />

    <xsl:template match="root">
        <section class="section-bordered">
            <xsl:if test="periods = 1">
                <form name="filter_lids" id="filter_lids">
                    <div class="row finances-calendar">
                        <div class="right">
                            <h4>Период с:</h4>
                        </div>

                        <div>
                            <input type="date" class="form-control" name="date_from" value="{//date_from}"/>
                        </div>

                        <div class="right">
                            <h4>по:</h4>
                        </div>

                        <div>
                            <input type="date" class="form-control" name="date_to" value="{//date_to}"/>
                        </div>

                        <xsl:call-template name="areas_row" />
                        <!--<div>-->
                            <!--<a class="btn btn-purple lids_show">Показать</a>-->
                        <!--</div>-->
                    </div>
                    <div class="row buttons-panel center">
                        <!--<xsl:call-template name="areas_row" />-->
                        <div>
                            <input class="form-control" type="number" name="id" placeholder="Номер лида" value="{/root/id}"/>
                        </div>
                        <div>
                            <input class="form-control" type="text" name="number" placeholder="Телефон" value="{/root/number}"/>
                        </div>

                        <div class="right">
                            <h4>Статусы:</h4>
                        </div>
                        <div>
                            <select name="status_id" class="form-control">
                                <option value="0"> ... </option>
                                <xsl:for-each select="lid_status">
                                    <xsl:variable name="id" select="id" />
                                    <option value="{id}">
                                        <xsl:if test="$id = /root/status_id">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:value-of select="title" />
                                    </option>
                                </xsl:for-each>
                            </select>
                        </div>

                        <div>
                            <a class="btn btn-purple lids_search">Показать</a>
                        </div>

                        <xsl:if test="//access_lid_create = 1">
                            <div>
                                <a class="btn btn-purple" onclick="makeLidPopup(0)">Создать лида</a>
                            </div>
                        </xsl:if>
                    </div>
                </form>
            </xsl:if>
        </section>

        <section class="cards-section section-lids text-center">
            <div id="cards-wrapper" class="cards-wrapper row">
                <xsl:choose>
                    <xsl:when test="count(lid) != 0">
                        <xsl:apply-templates select="lid" />
                    </xsl:when>
                    <xsl:otherwise>
                        <h2 class="section-title">Ничего не найдено</h2>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
        </section>
    </xsl:template>



</xsl:stylesheet>