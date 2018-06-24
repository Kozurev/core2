<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div style="text-align: right; margin-bottom:20px">
            <a class="btn btn-pink certificate_create">Добавить сертификат</a>
        </div>

        <table id="sortingTable" class="table table-striped certificate">
            <thead>
                <tr class="header">
                    <th>№</th>
                    <th>Дата продажи</th>
                    <th>Действителен до</th>
                    <th>Номер</th>
                    <th>Примечание</th>
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
                <a class="btn btn-pink certificate_delete" data-id="{id}">Удалить</a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>