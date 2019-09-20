<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:xsk="http://www.w3.org/1999/XSL/Transform">


    <xsl:template match="root">
        <xsl:variable name="selectedMarkerId" select="markerId" />
        <xsl:variable name="selectedSourceId" select="sourceId" />

        <section>
            <h3>Сводка по источникам/маркерам</h3>

            <div class="row">
                <div class="col-md-2 col-xs-4 right">
                    <h4>Маркер:</h4>
                </div>
                <div class="col-md-2 col-xs-8">
                    <select class="form-control" id="lid_statistic_markerId">
                        <option value="0"> ... </option>
                        <xsl:for-each select="filters/marker">
                            <option value="{id}">
                                <xsl:if test="id = $selectedMarkerId">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="value" />
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
                <!--<div class="col-md-2 col-xs-4 right">-->
                    <!--<h4>Источник:</h4>-->
                <!--</div>-->
                <!--<div class="col-md-2 col-xs-8">-->
                    <!--<select class="form-control" id="lid_statistic_sourceId">-->
                        <!--<option value="0"> ... </option>-->
                        <!--<xsl:for-each select="filters/source">-->
                            <!--<option value="{id}">-->
                                <!--<xsl:if test="id = $selectedSourceId">-->
                                    <!--<xsl:attribute name="selected">selected</xsl:attribute>-->
                                <!--</xsl:if>-->
                                <!--<xsl:value-of select="value" />-->
                            <!--</option>-->
                        <!--</xsl:for-each>-->
                    <!--</select>-->
                <!--</div>-->
                <!--<div class="col-md-2 col-xs-12">-->
                    <!--<a class="btn btn-orange btn-block lids_statistic_show">Показать</a>-->
                <!--</div>-->
            </div>

            <!--<xsl:choose>-->
                <!--<xsl:when test="$selectedMarkerId = 0 and $selectedSourceId = 0">-->
                    <!--<h4>Укажите минимум один параметр</h4>-->
                <!--</xsl:when>-->
                <!--<xsl:otherwise>-->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered sortingTable">
                            <thead>
                                <tr>
                                    <th>Источник</th>
                                    <th class="right">Всего</th>
                                    <xsl:for-each select="/root/lid_status">
                                        <th class="center"><xsl:value-of select="title" /></th>
                                    </xsl:for-each>
                                </tr>
                            </thead>
                            <tbody>
                                <xsl:apply-templates select="source" />
                            </tbody>
                        </table>
                    </div>
                <!--</xsl:otherwise>-->
            <!--</xsl:choose>-->
        </section>
    </xsl:template>


    <xsl:template match="source">
        <tr>
            <td>
                <xsl:value-of select="value" />
            </td>
            <td class="right"><xsl:value-of select="total_count" /></td>
            <xsl:apply-templates select="status" />
        </tr>
    </xsl:template>


    <xsl:template match="status">
        <td class="center">
            <xsl:value-of select="count_lids" />
        </td>
    </xsl:template>

</xsl:stylesheet>