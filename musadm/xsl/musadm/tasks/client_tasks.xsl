<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <style>
            .tasks {
                margin-top: 40px;
                margin-bottom: 40px;
            }

            .tasks .positive {
                background-color: #78FC5A !important;
                background-color: rgba(120, 252, 90, 0.7) !important;
                border-color: rgba(120, 252, 90, 0.7) !important;
            }
        </style>

        <input id="table_type" type="hidden" value="{table_name}" />

        <xsl:choose>
            <xsl:when test="count(task) != 0">
                <div class="table-responsive">
                    <table id="sortingTable" class="table table-bordered task center">
                        <thead>
                            <tr class="header">
                                <th>№</th>
                                <th>Дата контроля</th>
                                <th>Примечания</th>
                                <th>Действия</th>
                            </tr>
                        </thead>

                        <tbody>
                            <xsl:apply-templates select="task[done = 0]" />
                            <xsl:apply-templates select="task[done = 1]" />
                        </tbody>
                    </table>
                </div>
            </xsl:when>
            <xsl:otherwise>
                Задачь связанных с данным клиентом не найдено.
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template match="task">
        <xsl:variable name="id" select="id" />

        <xsl:variable name="class">
            <xsl:if test="done = 1">positive</xsl:if>
        </xsl:variable>

        <tr class="{$class}">
            <td><xsl:value-of select="id" /></td>
            <td>
                <span><xsl:value-of select="date" /></span>
                <xsl:if test="done = 0">
                    <a href="#" class="action edit task_date_edit" data-task_id="{id}" title="Изменить дату контроля"></a>
                </xsl:if>
            </td>

            <td class="tasks-comments-td">
                <div class="tasks-comments">
                    <xsl:for-each select="/root/task_note[task_id = $id]" >
                        <div class="block">
                            <div class="comment_header">
                                <div class="author">
                                    <xsl:choose>
                                        <xsl:when test="author_id = 0">
                                            Система
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="surname" />
                                            <xsl:text> </xsl:text>
                                            <xsl:value-of select="name" />
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </div>
                                <div class="date">
                                    <xsl:value-of select="date" />
                                </div>
                            </div>

                            <div class="comment_body">
                                <p><xsl:value-of select="text" /></p>
                            </div>
                        </div>
                    </xsl:for-each>
                </div>
            </td>

            <td>
                <xsl:if test="done = 0">
                    <a href="#" class="action append_done task_append_done" data-task_id="{id}" title="Закрыть задачу"></a>
                    <a data-task_id="{id}" class="action comment task_add_note" data-table_type="{/root/table_name}" title="Добавить комментарий"></a>
                    <!--<a href="#" class="action associate associate" title="Привязать к клиенту" data-task_id="{id}"></a>-->
                </xsl:if>
            </td>
        </tr>
    </xsl:template>

    <!--<xsl:template match="task_note">-->
        <!--<td>-->
            <!--<span><xsl:value-of select="text" /></span>-->
        <!--</td>-->
    <!--</xsl:template>-->

</xsl:stylesheet>