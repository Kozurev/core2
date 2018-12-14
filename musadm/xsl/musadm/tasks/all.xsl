<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <input id="table_type" type="hidden" value="{table_name}" />

        <xsl:if test="periods = 1">
            <div class="row finances_calendar">
                <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                    <span>Период с:</span>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                    <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                </div>

                <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                    <span>по:</span>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                    <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-md-offset-1 col-xs-12">
                    <a class="btn btn-red tasks_show" >Показать</a>
                </div>
            </div>
        </xsl:if>

        <div class="row buttons-panel">
            <xsl:choose>
                <xsl:when test="periods = 1">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <a class="btn btn-red task_create">Добавить задачу</a>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <a class="btn btn-green task_create">Добавить задачу</a>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </div>

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
                <!--<xsl:if test="done = 0">-->
                    <br/><a href="#" class="action edit task_date_edit" data-task_id="{id}" title="Изменить дату контроля"></a>
                <!--</xsl:if>-->

                <xsl:if test="associate != 0">
                    <xsl:variable name="userid" select="associate" />
                    <xsl:variable name="fio">
                        <xsl:value-of select="//assignment[id = $userid]/surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="//assignment[id = $userid]/name" />
                    </xsl:variable>

                    <a class="user-icon" title="{$fio}" href="{//wwwroot}/balance?userid={//assignment[id = $userid]/id}"><input type="hidden" /></a>
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
                </xsl:if>
                    <a data-task_id="{id}" class="action comment task_add_note" data-table_type="{/root/table_name}" title="Добавить комментарий"></a>
                    <a href="#" class="action associate associate" title="Привязать к клиенту" data-task_id="{id}"></a>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="task_note">
        <td>
            <span><xsl:value-of select="text" /></span>
        </td>
    </xsl:template>

</xsl:stylesheet>