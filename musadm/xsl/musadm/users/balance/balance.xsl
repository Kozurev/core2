<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-info section-bordered">
            <h3>Общие сведения</h3>
            <div class="table-responsive">
                <table class="table table-hover" cellspacing='0'>
                    <tr>
                        <td>Баланс</td>
                        <td>
                            <xsl:call-template name="property">
                                <xsl:with-param name="id" select="'12'"/>
                            </xsl:call-template>
                        </td>
                        <td>
                            <xsl:if test="is_admin = 1">
                                <a class="action add_payment btn_balance" data-userid="{user/id}" title="Зачислить платеж">
                                    <!--Пополнить баланс-->
                                </a>
                            </xsl:if>
                        </td>
                    </tr>

                    <tr>
                        <td>Кол-во индивидуальных / групповых занятий</td>
                        <td>
                            <xsl:call-template name="property">
                                <xsl:with-param name="id" select="'13'"/>
                            </xsl:call-template>
                            <xsl:text> / </xsl:text>
                            <xsl:call-template name="property">
                                <xsl:with-param name="id" select="'14'"/>
                            </xsl:call-template>
                        </td>
                        <td>
                            <a class="action buy btn_private_lessons" data-userid="{user/id}">
                                <!--Купить индивидуальные занятия-->
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>Сменный график</td>

                        <td><!--dng--></td>

                        <td>
                            <input type="checkbox" id="per_lesson" class="checkbox-new" data-userid="{note/object_id}" >
                                <xsl:if test="per_lesson/value = 1">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                            </input>
                            <label for="per_lesson" class="label-new" style="position: relative; top: 5px;">
                                <div class="tick"><input type="hidden" name="kostul"/></div>
                            </label>
                        </td>
                    </tr>

                    <xsl:if test="absent/id != ''">
                        <tr>
                            <td>Текущий период отсутствия</td>

                            <td>
                                <span id="absent-from"><xsl:value-of select="absent/date_from" /></span>
                                <span> - </span>
                                <span id="absent-to"><xsl:value-of select="absent/date_to" /></span>
                            </td>

                            <td>
                                <a class="action edit edit_client_absent" data-id="{absent/id}"><xsl:text>&#x0A;</xsl:text></a>
                                <a class="action delete"><xsl:text>&#x0A;</xsl:text></a>
                            </td>
                        </tr>
                    </xsl:if>

                    <xsl:if test="entry/value != ''">
                        <tr>
                            <td>Последяя авторизация</td>
                            <td colspan="2"><xsl:value-of select="entry/value" /></td>
                        </tr>
                    </xsl:if>

                    <tr>
                        <td>Примечание</td>

                        <td colspan="2">
                            <textarea class="form-control" placeholder="Заметки" id="client_notes" data-userid="{note/object_id}" >
                                <xsl:choose>
                                    <xsl:when test="note/value != ''">
                                        <xsl:value-of select="note/value" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:text>&#x0A;</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </textarea>
                        </td>
                    </tr>

                </table>
            </div>
        </section>
    </xsl:template>


    <xsl:template name="property">
        <xsl:param name="id"/>

        <xsl:choose>
            <xsl:when test="property[property_id=$id]/value = ''">
                0
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="property[property_id=$id]/value" />
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>

</xsl:stylesheet>