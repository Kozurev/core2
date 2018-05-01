<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table class="simple-little-table balance_table" cellspacing='0'>
            <tr>
                <td>Баланс</td>
                <td>
                    <xsl:call-template name="property">
                        <xsl:with-param name="id" select="'12'"/>
                    </xsl:call-template>
                </td>
                <td>
                    <xsl:if test="user_group/id != 5">
                        <button class="btn btn-success btn_balance" data-userid="{user/id}">
                            Пополнить баланс
                        </button>
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
                    <button class="btn btn-success btn_private_lessons" data-userid="{user/id}">
                        <!-- <xsl:if test="property[property_id='12']/value = 0 and user_group/id != 5">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if> -->
                        Купить индивидуальные занятия
                    </button></td>
            </tr>

            <tr>
                <td>Кол-во групповых занятий</td>
                <td>
                    <xsl:call-template name="property">
                        <xsl:with-param name="id" select="'14'"/>
                    </xsl:call-template>
                </td>
                <td>
                    <button class="btn btn-success btn_group_lessons" data-userid="{user/id}">
                        <!-- <xsl:if test="property[property_id='12']/value = 0">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if> -->
                        Купить групповые занятия
                    </button>
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