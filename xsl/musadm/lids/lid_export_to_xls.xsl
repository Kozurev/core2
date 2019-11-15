<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="root">
        <html>
            <head>
                <meta http-equiv="content-type" content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
            </head>
            <body>
                <table>
                    <xsl:apply-templates select="lid" />
                </table>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="lid">
        <tr>
            <xsl:choose>
                <xsl:when test="//surname != ''">
                    <td>
                        <xsl:value-of select="surname" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="//name != '' and name != '' ">
                    <td>
                        <xsl:value-of select="name" />
                    </td>
                </xsl:when>
                <xsl:otherwise>

                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="//number != '' ">
                    <td>
                        <xsl:value-of select="number" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="// vk != '' ">
                    <td>
                        <xsl:value-of select="vk" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="//control_date != '' ">
                    <td>
                        <xsl:value-of select="control_date" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="//area_id != '' and //area_id != 0 ">
                    <xsl:variable name="area_id" select="area_id"/>
                    <td>
                        <xsl:value-of select="/root/schedule_area[id=$area_id]/title" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="//status_id != '' and //status_id != 0 ">
                    <xsl:variable name="status_id" select="status_id"/>
                    <td>
                        <xsl:value-of select="/root/lid_status[id=$status_id]/title" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="source != '' or property_value[property_id = 50]/value_id != 0 ">
                    <xsl:variable name="source">
                        <xsl:choose>
                            <xsl:when test="property_value[property_id = 50]/value_id > 0">
                                <xsl:variable name="sourceId" select="property_value[property_id = 50]/value_id" />
                                <xsl:value-of select="//property_list_values[id=$sourceId]/value" />
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="source" />
                            </xsl:otherwise>
                        </xsl:choose>

                    </xsl:variable>
                    <td>
                        <xsl:value-of select="$source" />
                    </td>
                </xsl:when>
                <xsl:when test="source = '' and property_value[property_id = 50]/value_id = 0 "> <td></td></xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="property_value[property_id = 54]/value_id != 0 ">
                    <xsl:variable name="markerId" select="property_value[property_id = 54]/value_id" />
                    <td>
                        <xsl:value-of select="//property_list_values[id=$markerId]/value" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="property_value[property_id = 20]/value_id != 0 ">
                    <xsl:variable name="instrumentId" select="property_value[property_id = 20]/value_id" />
                    <td>
                        <xsl:value-of select="//property_list_values[id=$instrumentId]/value" />
                    </td>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>
        </tr>
    </xsl:template>
</xsl:stylesheet>