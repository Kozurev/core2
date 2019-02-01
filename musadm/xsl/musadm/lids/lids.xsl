<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <!--<div class="lids">-->

            <xsl:if test="periods = 1">
                <div class="row finances_calendar buttons-panel center">
                    <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                        <span>Период с:</span>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                        <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                    </div>

                    <div class="right col-lg-1 col-md-1 col-sm-1 col-xs-4">
                        <span>по:</span>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                        <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                        <a class="btn btn-purple lids_show">Показать</a>
                    </div>
                </div>
            </xsl:if>


            <xsl:if test="buttons-panel = 1">
                <div class="row buttons-panel center">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <input class="form-control" type="number" id="search_id" placeholder="Номер лида" value="{lid_id}" />
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
                        <a class="btn btn-purple search">Поиск</a>
                    </div>

                    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                        <a class="btn btn-purple create_lid">Создать лида</a>
                    </div>
                </div>
            </xsl:if>

            <!--<div class="table-responsive">-->
                <!--<table class="table lids">-->
                    <!--<form name="lid_form">-->
                        <!--<tr class="header">-->
                            <!--<td class="date" colspan="2"><input type="date" class="form-control date_inp"    name="control_date"/></td>-->
                            <!--<td class="string"><input type="text" class="form-control" name="surname"  placeholder="Фамилия"/></td>-->
                            <!--<td class="string"><input type="text" class="form-control" name="name"     placeholder="Имя"/></td>-->
                            <!--<td class="string"><input type="text" class="form-control" name="number"   placeholder="Телефон"/></td>-->
                            <!--<td class="string"><input type="text" class="form-control" name="vk"       placeholder="Ссылка вк"/></td>-->
                            <!--<td class="string"><input type="text" class="form-control" name="source"   placeholder="Источник"/></td>-->
                            <!--<td class="last">-->
                                <!--<a class="btn btn-purple lid_submit">Добавить</a>-->
                            <!--</td>-->
                        <!--</tr>-->
                        <!--<tr class="header">-->
                            <!--<td colspan="8">-->
                                <!--<input type="text" class="form-control" name="comment"  placeholder="Комментарий"/>-->
                            <!--</td>-->
                        <!--</tr>-->
                    <!--</form>-->
                <!--</table>-->
            <!--</div>-->

            <section class="cards-section text-center">
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
        <!--</div>-->
    </xsl:template>


    <xsl:template match="lid">
        <xsl:variable name="statusId" select="status_id" />

        <div class="item {//lid_status[id = $statusId]/item_class}">
            <div class="item-inner">
                <h3 class="title">
                    <xsl:value-of select="id" /> <xsl:text> </xsl:text>
                    <xsl:value-of select="surname" /><xsl:text> </xsl:text>
                    <xsl:value-of select="name" /><xsl:text> </xsl:text>
                    <xsl:value-of select="patronimyc" /><xsl:text> </xsl:text>
                </h3>

                <xsl:if test="number != ''">
                    <p class="intro">
                        <span>Телефон: </span><xsl:value-of select="number" />
                    </p>
                </xsl:if>

                <xsl:if test="vk != ''">
                    <p class="intro">
                        <span>ВК: </span><xsl:value-of select="vk" />
                    </p>
                </xsl:if>

                <xsl:if test="source != ''">
                    <p class="intro">
                        <span>Источник: </span><xsl:value-of select="source" />
                    </p>
                </xsl:if>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="date" class="form-control date_inp lid_date" data-lidid="{id}" >
                            <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                        </input>
                    </div>

                    <div class="col-ld-6 col-md-6 col-sm-12 col-xs-12">
                        <select name="status" class="form-control lid_status" data-lidid="{id}">
                            <!--<xsl:variable name="status_id" select="property_value/id" />-->
                            <xsl:for-each select="/root/lid_status">
                                <xsl:variable name="id" select="id" />
                                <option value="{$id}">
                                    <xsl:if test="$id = $statusId">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="title" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-ld-6 col-md-6 col-sm-12 col-xs-12">
                        <select class="form-control lid-area" data-lid-id="{id}">
                            <option value="0"> ... </option>
                            <xsl:variable name="areaId" select="area_id" />
                            <xsl:for-each select="//schedule_area">
                                <option value="{id}">
                                    <xsl:if test="id = $areaId">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="title" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 center">
                        <a class="action comment add_lid_comment" data-lidid="{id}" title="Добавить комментарий"><input type="hidden" value="kostul" /></a>
                    </div>
                </div>

                <div class="comments">
                    <input type="hidden" value="KOCTb|J|b" />
                    <xsl:for-each select="comments/lid_comment">
                        <xsl:variable name="author" select="author_id" />
                        <div class="block">
                            <div class="comment_header">
                                <div class="author">
                                    <xsl:value-of select="surname" />
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="name" />
                                </div>
                                <div class="date">
                                    <xsl:value-of select="datetime" />
                                </div>
                            </div>

                            <div class="comment_body">
                                <xsl:value-of select="text" />
                            </div>
                        </div>
                    </xsl:for-each>
                </div>

            </div><!--//item-inner-->
        </div><!--//item-->

    </xsl:template>


</xsl:stylesheet>