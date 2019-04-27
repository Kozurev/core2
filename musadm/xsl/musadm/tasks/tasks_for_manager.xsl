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
                    <a class="btn btn-red"
                       onclick="refreshTasksTable(
                            $('input[name=date_from]').val(),
                            $('input[name=date_to]').val(),
                            $('select[name=area_id]').val()
                        )">
                        Показать
                    </a>
                </div>
            </div>
        </xsl:if>

        <div class="row buttons-panel">
            <xsl:choose>
                <xsl:when test="periods = 1">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <a class="btn btn-red" onclick="newTaskPopup()">Добавить задачу</a>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                        <a class="btn btn-green" onclick="newTaskPopup()">Добавить задачу</a>
                    </div>
                </xsl:otherwise>
            </xsl:choose>
        </div>

        <section class="cards-section text-center tasks">
            <div id="cards-wrapper" class="cards-wrapper row">
                <xsl:apply-templates select="task[done = 0]" />
                <xsl:apply-templates select="task[done = 1]" />
            </div>
        </section>
    </xsl:template>


    <xsl:template match="task">
        <xsl:variable name="id" select="id" />

        <div >
            <xsl:choose>
                <xsl:when test="/root/card-size = 'small'">
                    <xsl:attribute name="class">item col-md-6 col-sm-6 col-xs-12</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="class">item col-md-12 col-sm-12 col-xs-12</xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>

            <div class="item-inner">
                <div class="row">
                    <!--Дата контроля-->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="date" class="form-control task_date" data-taskid="{id}" value="{date}" />
                    </div>

                    <!--Филлиал-->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select name="area" class="form-control" onchange="updateTaskArea({id}, this.value)">
                            <xsl:variable name="areaId" select="area_id" />
                            <option value="0"> ... </option>
                            <xsl:for-each select="//schedule_area">
                                <option value="{id}">
                                    <xsl:if test="id = $areaId">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="title" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <xsl:if test="done = 0">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <a href="#" class="action append_done task_append_done" data-task_id="{id}" title="Закрыть задачу"><input type="hidden" /></a>
                        </div>
                    </xsl:if>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <a data-task_id="{$id}" class="action comment task_add_note" data-table_type="{/root/table_name}" title="Добавить комментарий"><input type="hidden" /></a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <a href="#" class="action associate associate" title="Привязать к клиенту" data-task_id="{$id}"><input type="hidden" /></a>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <xsl:if test="associate != 0">
                            <xsl:variable name="fio">
                                <xsl:value-of select="user/surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="user/name" />
                            </xsl:variable>

                            <a class="user-icon" title="{$fio}" href="{//wwwroot}/balance?userid={user/id}" target="_blank"><input type="hidden" /></a>
                        </xsl:if>
                        <input type="hidden" />
                    </div>
                </div>

                <div class="row comments">
                    <xsl:apply-templates select="//task_note[task_id = $id]" />
                    <input type="hidden" />
                </div>

            </div>

        </div>
    </xsl:template>


    <xsl:template match="task_note">
        <div class="block">
            <div class="comment_header">
                <div class="author">
                    <xsl:choose>
                        <xsl:when test="author_id = 0">Система</xsl:when>
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
    </xsl:template>


</xsl:stylesheet>