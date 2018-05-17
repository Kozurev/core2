<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div style="text-align: right">
            <button class="btn btn-success task_create">Добавить задачу</button>
        </div>

        <table id="sortingTable" class="tablesorter">
            <thead>
                <tr>
                    <th class="header">№</th>
                    <th class="header">Дата</th>
                    <th>Примечания</th>
                    <th class="header">Тип</th>
                    <th class="header">Статус</th>
                    <th>Добавить</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="task" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="task">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <span><xsl:value-of select="date" /></span>
                <xsl:if test="type = 3">
                    <a href="#" class="action edit task_date_edit" data-task_id="{id}"></a>
                </xsl:if>
                <!--<xsl:choose>-->
                    <!--<xsl:when test="type = 3">-->
                        <!--<input type="date" class="form-control" style="max-width: 150px;" data-task_id="{id}" value="{date}" />-->
                    <!--</xsl:when>-->
                    <!--<xsl:otherwise>-->
                        <!--<xsl:value-of select="date" />-->
                    <!--</xsl:otherwise>-->
                <!--</xsl:choose>-->
            </td>
            <xsl:apply-templates select="task_note" />
            <td>
                <xsl:variable name="type" select="type" />
                <xsl:value-of select="//task_type[id = $type]/title" />
            </td>
            <td>
                <xsl:choose>
                    <xsl:when test="done = 1">
                        Сделано
                    </xsl:when>
                    <xsl:otherwise>
                        <button data-id="{id}" class="btn btn-primary" >Сделано</button>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <button data-task_id="{id}" class="btn btn-primary">Добавить примечание</button>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="task_note">
        <td>
            <span><xsl:value-of select="text" /></span>
        </td>
    </xsl:template>

</xsl:stylesheet>