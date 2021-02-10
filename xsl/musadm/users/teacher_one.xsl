<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../schedule/assignments/areas_assignments.xsl" />

	<xsl:template match="user">
        <xsl:variable name="groupId" select="group_id" />

        <tr>
            <td>
                <a href="{/root/wwwroot}/lk?userid={id}">
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
                    <xsl:value-of select="value" />
                    <br/>
                </xsl:for-each>
            </td>

            <td>
                <xsl:value-of select="comment" />
            </td>

            <td width="140px">
                <span>
                    <input class="checkbox red" id="checkbox{id}" type="checkbox" name="teacher_stop_list" data-user_id="{id}" >
                        <xsl:if test="property_value[property_id = 59]/value = 1">
                            <xsl:attribute name="checked">true</xsl:attribute>
                        </xsl:if>
                    </input>
                    <label for="checkbox{id}" class="checkbox-label">
                        <span class="off">Нет</span>
                        <span class="on">Да</span>
                    </label>
                </span>
            </td>
            <td>
                <span data-areas="User_{id}" class="teacher_areas"><xsl:apply-templates select="schedule_area_assignment" /></span>
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