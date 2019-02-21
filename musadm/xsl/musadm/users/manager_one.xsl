<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../schedule/assignments/areas_assignments.xsl" />

    <xsl:template match="user">
        <tr>
            <td>
                <a href="{/root/wwwroot}/authorize?auth_as={id}">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="patronimyc" />
                </a>
            </td>
            <!--<td><xsl:value-of select="name" /></td>-->
            <td><xsl:value-of select="phone_number" /><br/></td>

            <td>
                <span data-areas="User_{id}"><xsl:apply-templates select="schedule_area_assignment" /></span>
            </td>

            <td width="140px" class="right">
                <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="User"></a>
                <a class="action archive user_archive" href="#" data-userid="{id}"></a>
            </td>

        </tr>
    </xsl:template>

</xsl:stylesheet>