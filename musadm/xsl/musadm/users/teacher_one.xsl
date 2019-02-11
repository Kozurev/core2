<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../schedule/assignments/areas_assignments.xsl" />

	<xsl:template match="user">
        <xsl:variable name="groupId" select="group_id" />
        <xsl:variable name="instrumentId" select="property_value[property_id = 20]/value_id" />

        <tr>
            <td>
                <a href="{/root/wwwroot}/schedule/?userid={id}">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="patronimyc" />
                </a>
            </td>

            <td>
                <xsl:value-of select="phone_number" />
            </td>

            <td>
                <!--<xsl:value-of select="property_value[property_id = 20]/value" />-->
                <xsl:value-of select="/root/user_group[id = $groupId]/property[tag_name = 'instrument']/values/item[id = $instrumentId]/value" />
            </td>

            <td>
                <xsl:value-of select="property_value[property_id = 31]/value" />
            </td>

            <td>
                <span data-areas="User_{id}"><xsl:apply-templates select="schedule_area_assignment" /></span>
            </td>

            <td width="140px">
                <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="User"></a>
                <a class="action archive user_archive" href="#" data-userid="{id}"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>