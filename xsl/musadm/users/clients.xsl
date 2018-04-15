<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


    <xsl:template match="root">
        <table class="table">
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>Баланс</th>
                <th>Кол-во индив. занятий</th>
                <th>Кол-во групп. занятий</th>
                <th>Студия</th>
            </tr>
            <xsl:apply-templates select="user" />
        </table>
    </xsl:template>


    <xsl:template match="user">
        <tr>
            <td><xsl:value-of select="surname" /></td>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="phone" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>