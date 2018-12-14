<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <h3>Общие сведения</h3>
        <div class="table-responsive">
            <table class="table" cellspacing='0'>
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
                    <td rowspan="2">
                        <a class="action buy btn_private_lessons" data-userid="{user/id}">
                            <!--Купить индивидуальные занятия-->
                        </a>
                    </td>
                </tr>

                <!--<tr>-->
                    <!--<td>Кол-во групповых занятий</td>-->
                    <!--<td>-->
                        <!--<xsl:call-template name="property">-->
                            <!--<xsl:with-param name="id" select="'14'"/>-->
                        <!--</xsl:call-template>-->
                    <!--</td>-->
                    <!--<td>-->
                        <!--<a class="btn btn-orange btn_group_lessons" data-userid="{user/id}">-->
                            <!--Купить групповые занятия-->
                        <!--</a>-->
                    <!--</td>-->
                <!--</tr>-->
            </table>
        </div>
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