<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <input id="table_type" type="hidden" value="{table_name}" />

        <xsl:if test="table_name = 'all'">
            <div class="finances_calendar">
                Период
                с: <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                по: <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                <button class="btn btn-success tasks_show" >Показать</button>
            </div>
        </xsl:if>

        <div style="text-align: right">
            <button class="btn btn-success task_create">Добавить задачу</button>
        </div>

        <table id="sortingTable" class="tablesorter task">
            <thead>
                <tr>
                    <th class="header">№</th>
                    <th class="header">Дата контроля</th>
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
            <xsl:variable name="class">
                <xsl:if test="done = 1">positive</xsl:if>
            </xsl:variable>

            <td class="{$class}"><xsl:value-of select="id" /></td>
            <td class="{$class}">
                <span><xsl:value-of select="date" /></span>
                <xsl:if test="type = 3">
                    <a href="#" class="action edit task_date_edit" data-task_id="{id}"></a>
                </xsl:if>
            </td>

            <td class="{$class}">
                <xsl:for-each select="task_note" >
                    <div class="block">
                        <div class="comment_header">
                            <div class="author">
                                <xsl:value-of select="user/surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="user/name" />
                            </div>
                            <div class="date">
                                <xsl:value-of select="date" />
                            </div>
                        </div>

                        <div class="comment_body">
                            <xsl:value-of select="text" />
                        </div>
                    </div>
                </xsl:for-each>
            </td>

            <td class="{$class}">
                <xsl:variable name="type" select="type" />
                <xsl:value-of select="//task_type[id = $type]/title" />
            </td>
            <td class="{$class}">
                <xsl:choose>
                    <xsl:when test="done = 1">
                        <a href="#" class="action ok"></a>
                    </xsl:when>
                    <xsl:otherwise>
                        <!--<button data-task_id="{id}" class="btn btn-primary task_append_done" >Сделано</button>-->
                        <a href="#" class="action append_done task_append_done" data-task_id="{id}"></a>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="{$class}">
                <button data-task_id="{id}" class="btn btn-primary task_add_note" data-table_type="{/root/table_name}">Добавить примечание</button>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="task_note">
        <td>
            <span><xsl:value-of select="text" /></span>
        </td>
    </xsl:template>

</xsl:stylesheet>