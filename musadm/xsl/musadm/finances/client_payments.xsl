<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="rate_config.xsl" />


    <xsl:template match="root">

        <section>
            <div class="row finances_total">
                <div class="col-lg-12">
                    <h4>За данный период суммарные поступления составили <xsl:value-of select="total_summ" /> руб.</h4>
                </div>
            </div>

            <div class="row finances_calendar">
                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                    <a class="btn btn-green finances_payment" data-after_save_action="payments">Добавить расход</a>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                    <a class="btn btn-green tarifs_show">Тарифы</a>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                    <a class="btn btn-green finances_payment_types">Категории расходов</a>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                    <a class="btn btn-green finances_payment_rate_config">Настройки</a>
                </div>
            </div>
        </section>


        <div class="teacher_rate_config_block">
            <xsl:call-template name="rate_config_table" >
                <xsl:with-param name="director_id" select="director_id" />
                <xsl:with-param name="teacher_indiv_rate" select="teacher_indiv_rate" />
                <xsl:with-param name="teacher_group_rate" select="teacher_group_rate" />
                <xsl:with-param name="teacher_consult_rate" select="teacher_consult_rate" />
                <xsl:with-param name="absent_rate" select="absent_rate" />
                <xsl:with-param name="absent_rate_type" select="absent_rate_type" />
                <xsl:with-param name="absent_rate_val" select="absent_rate_val" />
            </xsl:call-template>
        </div>

        <div class="tarifs table-responsive">
            <table  class="table table-striped table-statused">
                <thead>
                    <tr class="header">
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Индив.</th>
                        <th>Групп.</th>
                        <th>Публичность</th>
                        <th>Действия</th>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="payment_tarif" />
                </tbody>
            </table>

            <div class="row buttons-panel">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a class="btn btn-green tarif_edit" href="#" data-tarifid="">Создать тариф</a>
                </div>
            </div>
        </div>


        <section>
            <div class="row finances_calendar" style="margin-top:20px">
                <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                    <h4>Период с:</h4>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                    <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                </div>

                <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                    <h4>по:</h4>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                    <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-md-offset-1 col-xs-12">
                    <a class="btn btn-green finances_show" >Показать</a>
                </div>
            </div>
        </section>

        <div class="table-responsive">
            <table id="sortingTable" class="table table-striped task">
                <thead>
                    <tr class="header">
                        <th>№</th>
                        <th>ФИО</th>
                        <th>Сумма</th>
                        <th>Примечание</th>
                        <th>Дата</th>
                        <th>Студия</th>
                        <th>Категория</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="payment" />
                </tbody>
            </table>
        </div>

    </xsl:template>


    <xsl:template match="payment">
        <xsl:variable name="type" select="type" />
        <xsl:variable name="areaId" select="area_id" />

        <tr>
            <td><xsl:value-of select="position()" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="user/surname != ''">
                        <xsl:value-of select="user/surname" />
                        <xsl:text>  </xsl:text>
                        <xsl:value-of select="user/name" />
                    </xsl:when>
                    <xsl:otherwise>
                        Расходы организации
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td><xsl:value-of select="value" /></td>
            <td><xsl:value-of select="description" /></td>
            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="//schedule_area[id = $areaId]/title" /></td>
            <td><xsl:value-of select="//payment_type[id = $type]/title" /></td>
            <td><a class="action edit payment_edit" href="#" data-id="{id}" data-after_save_action="payment" data-type="{type}" title="Редактирование платежа"></a></td>
        </tr>
    </xsl:template>


    <xsl:template match="payment_tarif">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td><xsl:value-of select="price" /></td>
            <td><xsl:value-of select="count_indiv" /></td>
            <td><xsl:value-of select="count_group" /></td>
            <td>
                <input type="checkbox" name="access" id="access{id}" class="checkbox-new" disabled="true" >
                    <xsl:if test="access = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="access{id}" class="label-new label-new-disabled">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
            </td>
            <td>
                <a class="action edit tarif_edit"       href="#" data-tarifid="{id}"></a>
                <a class="action delete tarif_delete"   href="#" data-model_id="{id}" data-model_name="Payment_Tarif"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>