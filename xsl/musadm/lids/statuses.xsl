<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="filter_lids_statistic" id="filter_lids_statistic">
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

                <div>
                    <a class="btn btn-orange lids_statistic_show">Показать</a>
                </div>
            </div>
        </form>
        <div class="row buttons-panel center">
            <div>
                <a class="btn btn-orange show_lid_status" data-lidid="">Статусы</a>
            </div>
            <div>
                <a class="btn btn-orange edit_property_list" data-prop-id="50">Источник</a>
            </div>
            <div>
                <a class="btn btn-orange edit_property_list" data-prop-id="54">Маркер</a>
            </div>
        </div>

        <section class="section-bordered lid_statuses_table">
            <h3 class="section-title">Настройки статусов лидов</h3>
            <input type="hidden" id="directorid" value="{directorid}"/>
            <table class="table table-stripped" id="table-lid-statuses">
                <thead>
                    <tr class="header">
                        <th class="center">Название</th>
                        <th class="center">Цвет</th>
                        <th class="center">Статус после создания консультации</th>
                        <th class="center">Статус после присутствия на консультации</th>
                        <th class="center">Статус после отсутствия на консультации</th>
                        <th class="center">Статус после того как лид стал клиентом</th>
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

                        <td class="center">
                            <input type="radio" name="lid_status_client" id="lid_status_client_0" value="0">
                                <xsl:if test="lid_status_client = 0">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                            </input>
                            <label for="lid_status_client_0"></label>
                        </td>

                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="row buttons-panel center">
                <div>
                    <a class="edit_lid_status btn btn-orange">Создать статус</a>
                </div>
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

            <td class="center">
                <input type="radio" name="lid_status_client" id="lid_status_client_{id}" value="{id}">
                    <xsl:if test="/root/lid_status_client = $id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="lid_status_client_{id}"></label>
            </td>

            <td class="right">
                <a class="action edit edit_lid_status" data-id="{id}"></a>
                <a class="action delete delete_lid_status" data-id="{id}"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>