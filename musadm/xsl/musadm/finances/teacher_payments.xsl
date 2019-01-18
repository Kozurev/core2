<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <h3>История выплат</h3>

        <div class="teacher_payments_block table-responsive">
            <table class="table table-striped teacher_payments">
                <xsl:if test="is_admin = 1">
                    <tr class="header">
                        <td><input name="date" class="form-control" type="date" value="{date}"/></td>
                        <td><input name="summ" class="form-control" type="number" placeholder="Сумма" /></td>
                        <td><input name="description" class="form-control" type="text" placeholder="Примечание к платежу" /></td>
                        <td class="center">
                            <input type="hidden" name="userid" value="{userid}" />
                            <!--<a class="btn btn-green add_teacher_payment">Добавить</a>-->
                            <a class="action pay add_teacher_payment" title="Добавить выплату"></a>
                        </td>
                    </tr>
                </xsl:if>
                <tr>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Примечание к платежу</th>
                    <th></th>
                </tr>
                <xsl:choose>
                    <xsl:when test="count(month) != 0">
                        <xsl:apply-templates select="month" />
                    </xsl:when>
                    <xsl:otherwise>
                        <tr>
                            <td colspan="4">Выплат не найдено</td>
                        </tr>
                    </xsl:otherwise>
                </xsl:choose>
            </table>
        </div>

    </xsl:template>


    <xsl:template match="month">
        <tr>
            <th colspan="4">
                <xsl:value-of select="month_name" />
            </th>
        </tr>

        <xsl:for-each select="payment">
            <tr>
                <td><xsl:value-of select="datetime" /></td>
                <td class="value"><xsl:value-of select="value" /></td>
                <td>
                    <xsl:if test="/root/is_director = 0"><xsl:attribute name="colspan">2</xsl:attribute></xsl:if>
                    <xsl:value-of select="description" />
                </td>
                <xsl:if test="/root/is_director = 1">
                    <td class="right">
                        <div class="row">
                            <!--<div class="col-lg-6 col-sm-6 col-xs-12">-->
                                <!--<a class="btn btn-orange payment_edit" href="#" data-id="{id}" data-after_save_action="teacher">Редактировать</a>-->
                                <a class="action edit payment_edit" href="#" data-id="{id}" data-after_save_action="teacher" title="Редактировать платеж"></a>
                            <!--</div>-->
                            <!--<div class="col-lg-6 col-sm-6 col-xs-12">-->
                                <!--<a class="btn btn-red payment_delete" href="#" data-id="{id}" data-after_save_action="teacher">Удалить</a>-->
                                <a class="action delete payment_delete" href="#" data-id="{id}" data-after_save_action="teacher" title="Удалить платеж"></a>
                            <!--</div>-->
                        </div>
                    </td>
                </xsl:if>
            </tr>
        </xsl:for-each>

    </xsl:template>


    <xsl:template name="payment">
        <tr>
            <td><xsl:value-of select="datetime" /></td>
            <td colspan="2"><xsl:value-of select="value" /></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>