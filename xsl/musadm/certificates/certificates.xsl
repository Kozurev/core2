<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div style="text-align: right">
            <button class="btn btn-success certificate_create">Добавить сертификат</button>
        </div>

        <table id="sortingTable" class="tablesorter certificate">
            <thead>
                <tr>
                    <th class="header">№</th>
                    <th class="header">Дата продажи</th>
                    <th class="header">Действителен до</th>
                    <th class="header">Номер</th>
                    <th class="header">Примечание</th>
                    <th>Удаление</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="certificate" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="certificate">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="sell_date" /></td>
            <td><xsl:value-of select="active_to" /></td>
            <td><xsl:value-of select="number" /></td>
            <td><xsl:value-of select="note" /></td>
            <td>
                <button class="btn btn-danger certificate_delete" data-id="{id}">Удалить</button>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>