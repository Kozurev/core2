<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../schedule/assignments/areas_assignments.xsl" />

	<xsl:template match="user">
        <xsl:variable name="groupId" select="group_id" />
        <!--<xsl:variable name="instrumentId" select="property_value[property_id = 20]/value_id" />-->

        <tr>
            <td>
                <a href="{/root/wwwroot}/schedule/?userid={id}">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="patronymic" />
                </a>
                <xsl:if test="property_value[property_id = 28]/value">
                    <br/><xsl:value-of select="property_value[property_id = 28]/value" />
                </xsl:if>
            </td>

            <td>
                <xsl:value-of select="phone_number" />
            </td>

            <td>
                <xsl:for-each select="property_value[property_id = 20]">
                    <xsl:variable name="instrumentId" select="value_id" />
                    <xsl:value-of select="/root/user_group[id = $groupId]/property[tag_name = 'instrument']/values/item[id = $instrumentId]/value" />
                    <br/>
                </xsl:for-each>
            </td>

            <td>
                <xsl:value-of select="property_value[property_id = 31]/value" />
            </td>

            <td>
                <span data-areas="User_{id}"><xsl:apply-templates select="schedule_area_assignment" /></span>
            </td>

            <td width="140px">
                <xsl:if test="//access_user_edit_teacher = 1">
                    <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                </xsl:if>

                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="User"></a>

                <xsl:if test="//access_user_archive_teacher = 1">
                    <a class="action archive user_archive" href="#" data-userid="{id}"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>