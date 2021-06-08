<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="rate_config.xsl" />
    <xsl:include href="areas_select.xsl" />

    <xsl:template match="root">

        <section>
            <div class="row finances-calendar">
                <div>
                    <h4>Период с:</h4>
                </div>
                <div>
                    <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                </div>
                <div>
                    <h4>по:</h4>
                </div>
                <div>
                    <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                </div>
                <div>
                    <a class="btn btn-green finances_show">Показать</a>
                </div>
                <xsl:call-template name="areas_row" />
            </div>
        </section>

        <section>
            <div class="row finances_total">
                <div class="col-lg-12">
                    <h4>За данный период суммарные поступления составили <xsl:value-of select="total_income" /> руб.</h4>
                    <h4>За данный период суммарный кэшбэк составил <xsl:value-of select="total_cashback" /> руб.</h4>
                    <h4>За данный период суммарные расходы составили <xsl:value-of select="total_expenses" /> руб.</h4>
                </div>
            </div>

            <div class="row buttons-panel">
                <xsl:if test="access_payment_create_all = 1">
                    <div>
                        <a class="btn btn-green" onclick="makePaymentPopup(0, savePaymentCallback)">Добавить расход</a>
                    </div>
                </xsl:if>

                <xsl:if test="access_payment_tariff_read = 1">
                    <div>
                        <a class="btn btn-green tarifs_show">Тарифы</a>
                    </div>
                </xsl:if>

                <xsl:if test="access_payment_config = 1">
                    <div>
                        <a class="btn btn-green finances_payment_types">Категории расходов</a>
                    </div>
                </xsl:if>

                <xsl:if test="access_payment_config = 1">
                    <div>
                        <a class="btn btn-green finances_payment_rate_config">Настройки</a>
                    </div>
                </xsl:if>
            </div>
        </section>


        <xsl:if test="access_payment_config = 1">
            <div class="teacher_rate_config_block">
                <xsl:call-template name="rate_config_table" >
                    <xsl:with-param name="director_id" select="director_id" />
                    <xsl:with-param name="teacher_indiv_rate" select="teacher_indiv_rate" />
                    <xsl:with-param name="teacher_group_rate" select="teacher_group_rate" />
                    <xsl:with-param name="teacher_consult_rate" select="teacher_consult_rate" />
                    <xsl:with-param name="teacher_private_rate" select="teacher_private_rate" />
                    <xsl:with-param name="absent_rate" select="absent_rate" />
                    <xsl:with-param name="absent_rate_type" select="absent_rate_type" />
                    <xsl:with-param name="absent_rate_val" select="absent_rate_val" />
                    <xsl:with-param name="api_token" select="api_token" />
                    <xsl:with-param name="cashback" select="cashback" />
                </xsl:call-template>
            </div>
        </xsl:if>


        <xsl:if test="access_payment_tariff_read = 1">
            <section class="tarifs section-bordered">
                <div class="table-responsive">
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
                            <xsl:apply-templates select="payment_tariff" />
                        </tbody>
                    </table>

                    <xsl:if test="access_payment_tariff_create = 1">
                        <div class="row buttons-panel center">
                            <div>
                                <a class="btn btn-green tariff_edit" href="#" data-tariff_id="">Создать тариф</a>
                            </div>
                        </div>
                    </xsl:if>
                </div>
            </section>
        </xsl:if>


        <xsl:if test="access_payment_read = 1">
            <section>
                <div class="table-responsive">
                    <table id="sortingTable" class="table table-striped task">
                        <thead>
                            <tr class="header">
                                <th>№</th>
                                <th>ФИО</th>
                                <th>Автор</th>
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
            </section>
        </xsl:if>

    </xsl:template>


    <xsl:template match="payment">
        <xsl:variable name="type" select="type" />
        <xsl:variable name="areaId" select="area_id" />

        <tr id="payment_{id}">
            <!-- <td><xsl:value-of select="position()" /></td> -->
            <td><xsl:value-of select="id" /></td>
            <td class="user">
                <xsl:choose>
                    <xsl:when test="assignment_user/surname != ''">
                        <xsl:value-of select="assignment_user/surname" />
                        <xsl:text>  </xsl:text>
                        <xsl:value-of select="assignment_user/name" />
                    </xsl:when>
                    <xsl:otherwise>
                        Расходы организации
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="author">
                <xsl:choose>
                    <xsl:when test="author/surname != ''">
                        <xsl:value-of select="author/surname" />
                        <xsl:text>  </xsl:text>
                        <xsl:value-of select="author/name" />
                    </xsl:when>
                    <xsl:when test="author_fio != ''">
                        <xsl:value-of select="author_fio" />
                    </xsl:when>
                    <xsl:otherwise>
                        Неизвестно
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="value"><xsl:value-of select="value" /></td>
            <td class="description"><xsl:value-of select="description" /></td>
            <td class="date"><xsl:value-of select="datetime" /></td>
            <td class="area"><xsl:value-of select="//schedule_area[id = $areaId]/title" /></td>
            <td class="type"><xsl:value-of select="//payment_type[id = $type]/title" /></td>
            <td>
                <xsl:if test="(/root/access_payment_edit_client = 1 and type = 1)
                            or (/root/access_payment_edit_teacher = 1 and type = 3)
                            or (/root/access_payment_edit_all = 1 and type > 3)">
                    <a class="action edit" onclick="makePaymentPopup({id}, savePaymentCallback)" title="Редактирование платежа"></a>
                </xsl:if>
                <xsl:if test="(/root/access_payment_delete_client = 1 and type = 1)
                            or (/root/access_payment_delete_teacher = 1 and type = 3)
                            or (/root/access_payment_delete_all = 1 and type > 3)">
                    <a class="action delete" onclick="Payment.remove({id}, removePaymentCallback)" title="Удаление платежа"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="payment_tariff">
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
                <xsl:if test="/root/access_payment_tariff_edit = 1">
                    <a class="action edit tariff_edit" href="#" data-tariff_id="{id}"></a>
                    <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="Payment_Tariff"></a>
                </xsl:if>

                <xsl:if test="/root/access_payment_tariff_delete = 1">
                    <a class="action delete tariff_delete" href="#" data-model_id="{id}" data-model_name="Payment_Tariff"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>