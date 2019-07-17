<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="areas_select.xsl"/>

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
                            <input class="form-control" type="number" name="id" placeholder="Номер лида" />
                        </div>
                        <div>
                            <input class="form-control" type="text" name="number" placeholder="Телефон"/>
                        </div>

                        <div class="right">
                            <h4>Статусы:</h4>
                        </div>
                        <div>
                            <select name="status_id" class="form-control">
                                <option value="0"> ... </option>
                                <xsl:for-each select="lid_status">
                                    <option value="{id}"><xsl:value-of select="title" /></option>
                                </xsl:for-each>
                            </select>
                        </div>

                        <div>
                            <a class="btn btn-purple lids_search">Показать</a>
                        </div>
                    </div>
                </form>
            </xsl:if>

            <xsl:if test="buttons-panel = 1">
                <div class="row buttons-panel center">
                    <xsl:if test="//access_lid_create = 1">
                        <div>
                            <a class="btn btn-purple" onclick="makeLidPopup(0)">Создать лида</a>
                        </div>
                    </xsl:if>
                    <xsl:if test="is-director = 1">
                        <div>
                            <a class="btn btn-purple show_lid_status" data-lidid="">Статусы</a>
                        </div>
                        <div>
                            <a class="btn btn-purple edit_property_list" data-prop-id="50">Источник</a>
                        </div>
                        <div>
                            <a class="btn btn-purple edit_property_list" data-prop-id="54">Маркер</a>
                        </div>
                    </xsl:if>
                </div>
            </xsl:if>
        </section>

        <xsl:if test="is-director = 1">
            <section class="section-bordered lid_statuses_table">
                <h4 class="section-title">Настройки статусов лидов</h4>
                <input type="hidden" id="directorid" value="{directorid}"/>
                <table class="table table-stripped" id="table-lid-statuses">
                    <thead>
                        <tr class="header">
                            <th class="center">Название</th>
                            <th class="center">Цвет</th>
                            <th class="center">Статус после создания консультации</th>
                            <th class="center">Статус после присутствия на консультации</th>
                            <th class="center">Статус после отсутствия на консультации</th>
                            <th width="95px"></th>
                        </tr>
                    </thead>
                    <tbody class="center">
                        <xsl:apply-templates select="lid_status" />
                        <tr>
                            <td colspan="2">Отсутствует</td>
                            <td class="center">
                                <input type="radio" name="lid_status_consult" id="lid_status_consult_0" value="0">
                                    <xsl:if test="lid_status_consult = 0">
                                        <xsl:attribute name="checked">checked</xsl:attribute>
                                    </xsl:if>
                                </input>
                                <label for="lid_status_consult_0"></label>
                            </td>

                            <td class="center">
                                <input type="radio" name="lid_status_consult_attended" id="lid_status_consult_attended_0" value="0">
                                    <xsl:if test="lid_status_consult_attended = 0">
                                        <xsl:attribute name="checked">checked</xsl:attribute>
                                    </xsl:if>
                                </input>
                                <label for="lid_status_consult_attended_0"></label>
                            </td>

                            <td class="center">
                                <input type="radio" name="lid_status_consult_absent" id="lid_status_consult_absent_0" value="0">
                                    <xsl:if test="lid_status_consult_absent = 0">
                                        <xsl:attribute name="checked">checked</xsl:attribute>
                                    </xsl:if>
                                </input>
                                <label for="lid_status_consult_absent_0"></label>
                            </td>

                            <td></td>
                        </tr>
                    </tbody>
                </table>

                <div class="row buttons-panel center">
                    <div>
                        <a class="edit_lid_status btn btn-purple">Создать статус</a>
                    </div>
                </div>
            </section>
        </xsl:if>

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


    <xsl:template match="lid_status">
        <xsl:variable name="itemClass" select="item_class" />
        <xsl:variable name="id" select="id" />

        <tr>
            <td>
                <xsl:value-of select="title" />
            </td>

            <td>
                <xsl:value-of select="//color[class = $itemClass]/name" />
            </td>

            <td class="center">
                <input type="radio" name="lid_status_consult" id="lid_status_consult_{id}" value="{id}">
                    <xsl:if test="/root/lid_status_consult = $id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="lid_status_consult_{id}"></label>
            </td>

            <td class="center">
                <input type="radio" name="lid_status_consult_attended" id="lid_status_consult_attended_{id}" value="{id}">
                    <xsl:if test="/root/lid_status_consult_attended = $id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="lid_status_consult_attended_{id}"></label>
            </td>

            <td class="center">
                <input type="radio" name="lid_status_consult_absent" id="lid_status_consult_absent_{id}" value="{id}">
                    <xsl:if test="/root/lid_status_consult_absent = $id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="lid_status_consult_absent_{id}"></label>
            </td>

            <td class="right">
                <a class="action edit edit_lid_status" data-id="{id}"></a>
                <a class="action delete delete_lid_status" data-id="{id}"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="lid">
        <xsl:variable name="statusId" select="status_id" />

        <div class="item {//lid_status[id = $statusId]/item_class} lid_{id}">
            <div class="item-inner">
                <div class="row">
                    <div class="col-sm-3 col-xs-12">
                        <h3 class="title">
                            <span class="id"><xsl:value-of select="id" /> <xsl:text> </xsl:text></span>
                            <span class="surname"><xsl:value-of select="surname" /><xsl:text> </xsl:text></span>
                            <span class="name"><xsl:value-of select="name" /><xsl:text> </xsl:text></span>
                            <!--<xsl:value-of select="patronimyc" /><xsl:text> </xsl:text>-->
                        </h3>

                        <p class="intro">
                            <xsl:if test="number = ''">
                                <xsl:attribute name="style">display:none</xsl:attribute>
                            </xsl:if>
                            <span>Телефон: </span><span class="number"><xsl:value-of select="number" /></span>
                        </p>

                        <p class="intro">
                            <xsl:if test="vk = ''">
                                <xsl:attribute name="style">display:none</xsl:attribute>
                            </xsl:if>
                            <span>ВК: </span><span class="vk"><xsl:value-of select="vk" /></span>
                        </p>


                        <p class="intro">
                            <xsl:if test="property_value[property_id = 54]/value_id = 0">
                                <xsl:attribute name="style">display:none</xsl:attribute>
                            </xsl:if>
                            <xsl:variable name="markerId" select="property_value[property_id = 54]/value_id" />
                            <span>Маркер: </span><span class="marker"><xsl:value-of select="//property_list_values[id=$markerId]/value" /></span>
                        </p>

                        <xsl:variable name="source">
                            <xsl:choose>
                                <xsl:when test="property_value[property_id = 50]/value_id > 0">
                                    <xsl:variable name="sourceId" select="property_value[property_id = 50]/value_id" />
                                    <xsl:value-of select="//property_list_values[id=$sourceId]/value" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="source" />
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>

                        <p class="intro">
                            <xsl:if test="$source = ''">
                                <xsl:attribute name="style">display:none</xsl:attribute>
                            </xsl:if>
                            <span>Источник: </span><span  class="source"><xsl:value-of select="$source" /></span>
                        </p>

                        <input type="date" class="form-control date_inp lid_date" onchange="Lids.changeDate({id}, this.value)">
                            <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                            <xsl:if test="/root/access_lid_edit = 0">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                        </input>

                        <select name="status" class="form-control lid_status" onchange="Lids.changeStatus({id}, this.value, changeLidStatusCallback)">
                            <xsl:if test="/root/access_lid_edit = 0">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>

                            <option value="0"> ... </option>
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

                        <select class="form-control lid-area" onchange="Lids.changeArea({id}, this.value)">
                            <xsl:if test="/root/access_lid_edit = 0">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
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

                        <xsl:variable name="priorityId" select="priority_id" />
                        <select class="form-control lid_priority" onchange="Lids.changePriority({id}, this.value)">
                            <xsl:for-each select="//lid_priority" >
                                <option value="{id}">
                                    <xsl:if test="id = $priorityId">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="title" />
                                </option>
                            </xsl:for-each>
                        </select>

                        <xsl:if test="/root/access_lid_edit = 1">
                            <a class="action edit" onclick="makeLidPopup({id})" title="Редактировать лида"><input type="hidden" value="KOCTb|J|b" /></a>
                        </xsl:if>
                        <xsl:if test="/root/access_lid_comment = 1">
                            <a class="action comment" title="Добавить комментарий" onclick="makeLidCommentPopup(0, {id}, saveLidCommentCallback)"><input type="hidden" value="KOCTb|J|b" /></a>
                        </xsl:if>
                    </div>
                    <div class="col-sm-9 col-xs-12 comments-column">
                        <div class="comments">
                            <input type="hidden" value="KOCTb|J|b" />
                            <xsl:for-each select="comments/lid_comment[text != '']">
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
                    </div>
                </div>
            </div><!--//item-inner-->
        </div><!--//item-->

    </xsl:template>


</xsl:stylesheet>