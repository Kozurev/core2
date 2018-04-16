<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table id="sortingTable" class="tablesorter">
            <thead>
                <tr>
                    <th class="header">Фамилия</th>
                    <th class="header">Имя</th>
                    <th>Телефон</th>
                    <th class="header">Баланс</th>
                    <th class="header">Кол-во индив. занятий</th>
                    <th class="header">Кол-во групп. занятий</th>
                    <th class="header">Студия</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="user" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="user">
        <tr>
            <td>
                <a href="../?userid={id}">
                    <xsl:value-of select="surname" />
                </a>
            </td>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="phone" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>