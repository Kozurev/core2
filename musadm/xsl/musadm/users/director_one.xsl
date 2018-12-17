<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="user">

        <tr>
            <td><a href="{/root/wwwroot}/authorize?auth_as={id}"><xsl:value-of select="surname" /></a></td>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="patronimyc" /></td>
            <td><xsl:value-of select="phone_number" /><br/></td>
            <td><xsl:value-of select="email" /><br/></td>
            <td><xsl:value-of select="property_value[property_id = 33]/value" /><br/></td>
            <td><xsl:value-of select="property_value[property_id = 29]/value" /></td>
            <td><xsl:value-of select="property_value[property_id = 30]/value" /></td>

            <td>
                <a class="action edit user_edit"        href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                <a class="action archive user_archive"     href="#" data-userid="{id}"></a>
            </td>

        </tr>
    </xsl:template>
</xsl:stylesheet>