<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <input type="hidden" id="teacher_id" value="{teacher_id}" />

        <h3>Настройка тарифов</h3>
        <div class="table-responsive finances">
            <table class="table teacher_rate_config">
                <tr>
                    <th>Тип занятия</th>
                    <th>Общий</th>
                    <th>Тип тарифа</th>
                    <th width="5%">Индив.</th>
                    <th></th>
                </tr>
                <tr>
                    <td>Индивидуальное</td>

                    <td>
                        <span><xsl:value-of select="teacher_rate_indiv_default" /></span>
                    </td>

                    <td>
                        <input class="checkbox is_default_rate" data-prop-name="is_teacher_rate_default_indiv" id="checkbox1" type="checkbox" >
                            <xsl:if test="is_teacher_rate_default_indiv = 0">
                                <xsl:attribute name="checked">true</xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="checkbox1" class="checkbox-label">
                            <span class="off">общий</span>
                            <span class="on">индивидуальный</span>
                        </label>
                    </td>

                    <td>
                        <span class="indiv-rate"><xsl:value-of select="teacher_rate_indiv" /></span>
                        <input class="form-control edit_rate_value" data-prop-name="teacher_rate_indiv" type="number" style="display: none" />
                    </td>

                    <td>
                        <a class="action edit teacher_rate_edit"></a>
                        <a class="action save teacher_rate_save"></a>
                    </td>

                </tr>
                <tr>
                    <td>Групповое</td>

                    <td>
                        <span><xsl:value-of select="teacher_rate_gorup_default" /></span>
                    </td>

                    <td>
                        <input class="checkbox is_default_rate" data-prop-name="is_teacher_rate_default_group" id="checkbox2" type="checkbox" >
                            <xsl:if test="is_teacher_rate_default_gorup = 0">
                                <xsl:attribute name="checked">true</xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="checkbox2" class="checkbox-label">
                            <span class="off">общий</span>
                            <span class="on">индивидуальный</span>
                        </label>
                    </td>

                    <td>
                        <span class="indiv-rate"><xsl:value-of select="teacher_rate_group" /></span>
                        <input class="form-control edit_rate_value" data-prop-name="teacher_rate_group" type="number" style="display: none" />
                    </td>

                    <td>
                        <a class="action edit teacher_rate_edit"></a>
                        <a class="action save teacher_rate_save"></a>
                    </td>
                </tr>
                <tr>
                    <td>Консультация</td>

                    <td>
                        <span><xsl:value-of select="teacher_rate_consult_default" /></span>
                    </td>

                    <td>
                        <input class="checkbox is_default_rate" data-prop-name="is_teacher_rate_default_consult" id="checkbox3" type="checkbox" >
                            <xsl:if test="is_teacher_rate_default_consult = 0">
                                <xsl:attribute name="checked">true</xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="checkbox3" class="checkbox-label">
                            <span class="off">общий</span>
                            <span class="on">индивидуальный</span>
                        </label>
                    </td>

                    <td>
                        <span class="indiv-rate"><xsl:value-of select="teacher_rate_consult" /></span>
                        <input class="form-control edit_rate_value" data-prop-name="teacher_rate_consult" type="number" style="display: none" />
                    </td>

                    <td>
                        <a class="action edit teacher_rate_edit"></a>
                        <a class="action save teacher_rate_save"></a>
                    </td>
                </tr>

                <tr>
                    <td>Отсутствие клиента</td>

                    <td>
                        <xsl:choose>
                            <xsl:when test="teacher_rate_type_absent = 0">
                                <span>Пропорционально</span>
                            </xsl:when>
                            <xsl:otherwise>
                                <span><xsl:value-of select="teacher_rate_absent_default" /></span>
                            </xsl:otherwise>
                        </xsl:choose>
                    </td>

                    <td>
                        <input class="checkbox is_default_rate" data-prop-name="is_teacher_rate_default_absent" id="checkbox4" type="checkbox" >
                            <xsl:if test="is_teacher_rate_default_absent = 0">
                                <xsl:attribute name="checked">true</xsl:attribute>
                            </xsl:if>
                        </input>
                        <label for="checkbox4" class="checkbox-label">
                            <span class="off">общий</span>
                            <span class="on">индивидуальный</span>
                        </label>
                    </td>

                    <td>
                        <span class="indiv-rate"><xsl:value-of select="teacher_rate_absent" /></span>
                        <input class="form-control edit_rate_value" data-prop-name="teacher_rate_absent" type="number" style="display: none" />
                    </td>

                    <td>
                        <a class="action edit teacher_rate_edit"></a>
                        <a class="action save teacher_rate_save"></a>
                    </td>
                </tr>
            </table>
        </div>
    </xsl:template>

</xsl:stylesheet>