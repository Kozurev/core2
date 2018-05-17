<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div style="text-align: right">
            <button class="btn btn-success task_create" data-usergroup="5">Добавить задачу</button>
        </div>

        <table id="sortingTable" class="tablesorter">
            <thead>
                <tr>
                    <th class="header">№</th>
                    <th class="header">Дата</th>
                    <th>Примечания</th>
                    <th class="header">Тип</th>
                    <th class="header">Статус</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="task" />
            </tbody>
        </table>

    </xsl:template>

</xsl:stylesheet>