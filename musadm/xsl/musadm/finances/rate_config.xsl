<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template name="rate_config_table">
        <xsl:param name="director_id" />
        <xsl:param name="teacher_indiv_rate" />
        <xsl:param name="teacher_group_rate" />
        <xsl:param name="teacher_consult_rate" />
        <xsl:param name="absent_rate" />
        <xsl:param name="absent_rate_type" />
        <xsl:param name="absent_rate_val" />

        <section class="section-bordered">
            <input type="hidden" value="{$director_id}" id="director_id" />

            <div class="table-responsive">
                <table class="table" cellspacing="0">
                    <tbody>
                        <tr>
                            <td colspan="2" width="60%">Ставка преподавателя за индивидуальное занятие по умолчанию</td>
                            <td class="right">
                                <span class="current_value"><xsl:value-of select="$teacher_indiv_rate" /></span>
                                <input type="number" class="form-control edit_rate_value" data-prop-name="teacher_rate_indiv_default" value="{$teacher_indiv_rate}" />
                            </td>
                            <td class="right">
                                <a class="action edit edit_rate" title="Редактировать"></a>
                                <a class="action save save_rate" title="Сохранить"></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Ставка преподавателя за групповое занятие по умолчанию</td>
                            <td class="right">
                                <span class="current_value"><xsl:value-of select="$teacher_group_rate" /></span>
                                <input type="number" class="form-control edit_rate_value" data-prop-name="teacher_rate_group_default" value="{$teacher_group_rate}" />
                            </td>
                            <td class="right">
                                <a class="action edit edit_rate" title="Редактировать"></a>
                                <a class="action save save_rate" title="Сохранить"></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Ставка преподавателя за консультацию по умолчанию</td>
                            <td class="right">
                                <span class="current_value"><xsl:value-of select="$teacher_consult_rate" /></span>
                                <input type="number" class="form-control edit_rate_value" data-prop-name="teacher_rate_consult_default" value="{$teacher_consult_rate}" />
                            </td>
                            <td class="right">
                                <a class="action edit edit_rate" title="Редактировать"></a>
                                <a class="action save save_rate" title="Сохранить"></a>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Значение списываемое с клиента за пропущенное занятие</td>
                            <td class="right">
                                <span class="current_value"><xsl:value-of select="$absent_rate" /></span>
                                <input type="number" class="form-control edit_rate_value" data-prop-name="client_absent_rate" value="{$absent_rate}" />
                            </td>
                            <td class="right">
                                <a class="action edit edit_rate" title="Редактировать"></a>
                                <a class="action save save_rate" title="Сохранить"></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Ставка препода при отсутствии клиента</td>

                            <td class="left">
                                <input class="checkbox is_default_rate_director" data-prop-name="teacher_rate_type_absent_default" id="checkbox1" type="checkbox" >
                                    <xsl:if test="$absent_rate_type = 1">
                                        <xsl:attribute name="checked">true</xsl:attribute>
                                    </xsl:if>
                                </input>
                                <label for="checkbox1" class="checkbox-label">
                                    <span class="off">пропорционально</span>
                                    <span class="on">значение</span>
                                </label>
                            </td>

                            <td class="right">
                                <span class="current_value"><xsl:value-of select="$absent_rate_val" /></span>
                                <input class="form-control edit_rate_value" data-prop-name="teacher_rate_absent_default" type="number" style="display: none" />
                            </td>

                            <td class="right">
                                <a class="action edit edit_rate" title="Редактировать"></a>
                                <a class="action save save_rate" title="Сохранить"></a>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </xsl:template>

</xsl:stylesheet>