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
                    <xsl:value-of select="patronymic" />
                </a>
            </td>
            <td><xsl:value-of select="phone_number" /><br/></td>

            <td>
                <span data-areas="User_{id}"><xsl:apply-templates select="schedule_area_assignment" /></span>
            </td>

            <td width="140px" class="right">
                <xsl:if test="//access_user_edit_manager = 1">
                    <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                </xsl:if>

                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="User"></a>

                <xsl:if test="//access_user_archive_manager = 1">
                    <a class="action archive user_archive" href="#" data-userid="{id}"></a>
                </xsl:if>
            </td>

        </tr>
    </xsl:template>

</xsl:stylesheet>