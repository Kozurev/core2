<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div class="row finances_calendar">
            <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <span>Период с:</span>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                <input type="date" class="form-control" name="date_from" value="{date_from}"/>
            </div>

            <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <span>по:</span>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                <input type="date" class="form-control" name="date_to" value="{date_to}"/>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-md-offset-1 col-xs-12">
                <a class="btn btn-green finances_show" >Показать</a>
            </div>
        </div>

        <div class="row finances_calendar">
            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a class="btn btn-green finances_payment">Хозрасходы</a>
            </div>

            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a class="btn btn-green tarifs_show">Тарифы</a>
            </div>

            <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                <a class="btn btn-green finances_payment_types">Категории расходов</a>
            </div>
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


        <div class="row finances_total">
            <div class="col-lg-12">
                За данный период суммарные поступления составили <xsl:value-of select="total_summ" /> руб.
            </div>
        </div>

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
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="payment" />
                </tbody>
            </table>
        </div>

    </xsl:template>


    <xsl:template match="payment">
        <tr>
            <td><xsl:value-of select="position()" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="user/surname != ''">
                        <xsl:value-of select="user/surname" />
                        <xsl:text>  </xsl:text>
                        <xsl:value-of select="user/name" />
                    </xsl:when>
                    <xsl:when test="type = 4">
                        Хозрасходы
                    </xsl:when>
                    <xsl:otherwise>
                        Пользователь удален
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td><xsl:value-of select="value" /></td>
            <td>
                <xsl:if test="type = 3">
                    <xsl:text>Выплата преподавателю. </xsl:text>
                </xsl:if>
                <xsl:value-of select="description" />
            </td>
            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="area" /></td>
        </tr>
    </xsl:template>


    <xsl:template match="payment_tarif">
        <tr>
            <xsl:variable name="type_id" select="lessons_type" />

            <td><xsl:value-of select="title" /></td>
            <td><xsl:value-of select="price" /></td>
            <td><xsl:value-of select="count_indiv" /></td>
            <td><xsl:value-of select="count_group" /></td>
            <!--<td><xsl:value-of select="/root/schedule_lesson_type[id = $type_id]/title" /></td>-->
            <td>
                <input type="checkbox" name="access" id="access{id}" class="checkbox-new" disabled="true" >
                    <xsl:if test="access = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="access{id}" class="label-new">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
                <!--<input class="checkbox" id="checkbox{id}" type="checkbox" disabled="true">-->
                    <!--<xsl:if test="access = 1">-->
                        <!--<xsl:attribute name="checked">true</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->
                <!--<label for="checkbox{id}" class="checkbox-label">-->
                    <!--<span class="off">скрытый</span>-->
                    <!--<span class="on">общий</span>-->
                <!--</label>-->
            </td>
            <td>
                <a class="action edit tarif_edit"       href="#" data-tarifid="{id}"></a>
                <a class="action delete tarif_delete"   href="#" data-model_id="{id}" data-model_name="Payment_Tarif"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>