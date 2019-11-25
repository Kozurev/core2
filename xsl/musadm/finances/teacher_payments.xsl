<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="section-bordered">
            <h3>История выплат</h3>

            <h4>
                К выплате: <span id='teacher-debt'><xsl:value-of select="debt" /></span>руб;
                Уже выплачено: <span id='teacher-payed'><xsl:value-of select="total-payed" /></span> руб.
            </h4>
            <h3>Премиальные</h3>

            <h4>
                К выплате: <span id='teacher-debt'><xsl:value-of select="debtAdditional" /></span>руб;
                Уже выплачено: <span id='teacher-payed'><xsl:value-of select="totalAdditionalPayed" /></span> руб.
            </h4>

            <div class="teacher_payments_block table-responsive">
                <table class="table table-striped teacher_payments">
                    <xsl:if test="access_payment_create = 1">
                        <tr>
                            <td><input name="date" id="paymentDate" class="form-control" type="date" value="{date}"/></td>
                            <td><input name="summ" id="paymentVal" class="form-control" type="number" placeholder="Сумма" /></td>
                            <td>
                                <select  name="payment" id="payment_type" class="form-control">
                                <option value="" style="display:none">Тип платежа</option>
                                <option value="3">Выплата преподавателю</option>
                                <option value="16">Начисление премиальных</option>
                                <option value="17">Выплата премии</option>
                                 </select>
                            </td>
                            <td colspan="2"><input name="description" class="form-control" type="text" placeholder="Примечание к платежу" /></td>
                            <td class="center">
                                <input type="hidden" name="userid" value="{userid}" />
                                <a class="action pay" onclick="Payment.save(0, {//userid}, $('#paymentVal').val(), $('#payment_type').val(), $('#paymentDate').val(), 0, $('input[name=description]').val(), '', saveTeacherPaymentCallback)" title="Добавить выплату"></a>
                            </td>
                        </tr>
                    </xsl:if>
                    <tr>
                        <th>Дата</th>
                        <th>Сумма</th>
                        <th>Тип платежа</th>
                        <th>Примечание к платежу</th>
                        <th>Автор</th>
                        <th><!----></th>
                    </tr>
                    <xsl:choose>
                        <xsl:when test="count(month) != 0">
                            <xsl:apply-templates select="month" />
                        </xsl:when>
                        <xsl:otherwise>
                            <tr>
                                <td colspan="5">Выплат не найдено</td>
                            </tr>
                        </xsl:otherwise>
                    </xsl:choose>
                </table>
            </div>
        </section>

    </xsl:template>


    <xsl:template match="month">
        <tr>
            <th>
                <xsl:value-of select="month_name" />
            </th>
            <th colspan="4">
                <span>Выплачено: </span>
                <p>Всего:<span class="month_total_pay"><xsl:value-of select="month_total_pay + month_additional_pay" /></span><br /></p>
<!--                Премиальных:<span class="month_total_pay"><xsl:value-of select="month_additional_pay" /></span></p>-->
<!--                <span>Начислено премиальных: </span>-->
<!--               <span class="month_total_pay"><xsl:value-of select="month_additional_add" /></span>-->
            </th>
        </tr>

        <xsl:for-each select="payment">
            <tr>
                <td><xsl:value-of select="datetime" /></td>
                <td class="value"><xsl:value-of select="value" /></td>
                <td>
                    <xsl:value-of select="payment_type" />
                </td>
                <td>
                    <xsl:value-of select="description" />
                </td>
                <td>
                    <xsl:value-of select="author_fio" />
                </td>
                <xsl:if test="/root/access_payment_edit != 0 or /root/access_payment_delete != 0">
                    <td class="right">
                        <div class="row">
                            <xsl:if test="/root/access_payment_edit = 1">
                                <a class="action edit" title="Редактировать платеж"
                                   onclick="makePaymentPopup({id}, refreshSchedule)" ></a>
                            </xsl:if>
                            <xsl:if test="/root/access_payment_delete = 1">
                                <a class="action delete" title="Удалить платеж"
                                    onclick="Payment.remove({id}, refreshSchedule)"></a>
                            </xsl:if>
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