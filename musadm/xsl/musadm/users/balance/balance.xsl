<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table class="table" cellspacing='0'>
            <tr>
                <td>Баланс</td>
                <td>
                    <xsl:call-template name="property">
                        <xsl:with-param name="id" select="'12'"/>
                    </xsl:call-template>
                </td>
                <td>
                    <xsl:if test="is_admin">
                        <a class="btn btn-orange btn_balance" data-userid="{user/id}">
                            Пополнить баланс
                        </a>
                    </xsl:if>
                </td>
            </tr>

            <tr>
                <td>Кол-во индивидуальных занятий</td>
                <td>
                    <xsl:call-template name="property">
                        <xsl:with-param name="id" select="'13'"/>
                    </xsl:call-template>
                </td>
                <td>
                    <a class="btn btn-orange btn_private_lessons" data-userid="{user/id}">
                        Купить индивидуальные занятия
                    </a></td>
            </tr>

            <tr>
                <td>Кол-во групповых занятий</td>
                <td>
                    <xsl:call-template name="property">
                        <xsl:with-param name="id" select="'14'"/>
                    </xsl:call-template>
                </td>
                <td>
                    <a class="btn btn-orange btn_group_lessons" data-userid="{user/id}">
                        <!-- <xsl:if test="property[property_id='12']/value = 0">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if> -->
                        Купить групповые занятия
                    </a>
                </td>
            </tr>
        </table>
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