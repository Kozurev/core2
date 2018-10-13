<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <input id="table_type" type="hidden" value="{table_name}" />

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

        <div class="row buttons-panel">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <a class="btn btn-red task_create">Добавить задачу</a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="sortingTable" class="table table-bordered task center">
                <thead>
                    <tr class="header">
                        <th>№</th>
                        <th>Дата контроля</th>
                        <th>Примечания</th>
                        <th>Статус</th>
                        <th>Добавить <br/> коммент.</th>
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

        <tr>
            <xsl:variable name="class">
                <xsl:if test="done = 1">positive</xsl:if>
            </xsl:variable>

            <td class="{$class}"><xsl:value-of select="id" /></td>
            <td class="{$class}">
                <span><xsl:value-of select="date" /></span>
                <a href="#" class="action edit task_date_edit" data-task_id="{id}"></a>
            </td>

            <td class="{$class}">
                <xsl:for-each select="/root/task_note[task_id = $id]" >
                    <div class="block">
                        <div class="comment_header">
                            <div class="author">
                                <xsl:value-of select="surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="name" />
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
                <xsl:choose>
                    <xsl:when test="done = 1">
                        <a href="#" class="action ok"></a>
                    </xsl:when>
                    <xsl:otherwise>
                        <a href="#" class="action append_done task_append_done" data-task_id="{id}"></a>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="{$class}">
                <a data-task_id="{id}" class="btn btn-red task_add_note" data-table_type="{/root/table_name}">+</a>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="task_note">
        <td>
            <span><xsl:value-of select="text" /></span>
        </td>
    </xsl:template>

</xsl:stylesheet>