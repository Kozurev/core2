<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table id="sortingTable" class="tablesorter">
            <thead>
                <tr>
                    <th class="header">Фамилия</th>
                    <th class="header">Имя</th>
                    <th class="header">Отчество</th>
                    <th class="header">Инструмент</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <xsl:apply-templates select="user" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="user">
        <tr>
            <td><a href="../?userid={id}"><xsl:value-of select="surname" /></a></td>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="patronimyc" /></td>
            <td><xsl:value-of select="property_value[property_id = 20]/value" /></td>
            <td></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>