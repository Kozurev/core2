<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="task">

        <xsl:if test="position() = 1">
            <xsl:for-each select="//task_priority" >
                <style>
                    #label_priority_<xsl:value-of select="id" />:before {
                    border-color: <xsl:value-of select="color" />;
                    }
                </style>
            </xsl:for-each>
        </xsl:if>

        <xsl:variable name="id" select="id" />
        <xsl:variable name="priorityId" select="priority_id" />

        <xsl:variable name="itemClass" >
            <xsl:choose>
                <xsl:when test="done = 1">
                    item-green
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="//task_priority[id = $priorityId]/item_class" />
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <div class="item {$itemClass}">

            <div class="item-inner">
                <div class="row">
                    <span class="form-control form-control-auto">№<xsl:value-of select="id" /></span>
                    <!--Дата контроля-->
                    <!--<div class="col-md-3 col-sm-6 col-xs-12">-->
                        <input type="date" class="form-control" value="{date}" onchange="updateTaskDate({id},this.value)">
                            <xsl:if test="/root/access_task_edit = 0">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                        </input>
                    <!--</div>-->

                    <!--Филлиал-->
                    <!--<div class="col-md-3 col-sm-6 col-xs-12">-->
                        <select name="area" class="form-control" onchange="updateTaskArea({id}, this.value)">
                            <xsl:if test="/root/access_task_edit = 0">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
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
                    <!--</div>-->

                    <xsl:if test="done = 0">
                        <!--<div class="col-md-1 col-sm-3 col-xs-3">-->
                            <xsl:if test="/root/access_task_edit = 1">
                                <a class="action append_done" onclick="markAsDone({id}, taskAfterAction)" title="Закрыть задачу"><input type="hidden" /></a>
                            </xsl:if>
                        <!--</div>-->
                    </xsl:if>
                    <!--<div class="col-md-1 col-sm-3 col-xs-3">-->
                    <xsl:if test="/root/access_task_append_comment = 1">
                        <a class="action comment task_add_note" title="Добавить комментарий" onclick="addTaskNotePopup({id})"><input type="hidden" /></a>
                    </xsl:if>
                    <!--</div>-->
                    <!--<div class="col-md-1 col-sm-3 col-xs-3">-->
                    <xsl:if test="/root/access_task_edit = 1">
                        <a class="action associate" title="Привязать к клиенту" onclick="assignmentTaskPopup({id})"><input type="hidden" /></a>
                    </xsl:if>
                    <!--</div>-->
                    <!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">-->

                    <xsl:if test="associate != 0">
                        <xsl:variable name="fio">
                            <xsl:value-of select="user/surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="user/name" />
                        </xsl:variable>

                        <a class="user-icon" title="{$fio}" href="{//wwwroot}/balance?userid={user/id}" target="_blank"><input type="hidden" /></a>
                    </xsl:if>
                        <!--<input type="hidden" />-->
                    <!--</div>-->
                </div>

                <div class="row center">
                    <xsl:for-each select="//task_priority">
                        <input type="radio" name="priority_{$id}" value="{id}" id="priority_{$id}_{id}">
                            <xsl:choose>
                                <xsl:when test="/root/access_task_edit = 1">
                                    <xsl:attribute name="onchange">
                                        loaderOn(); changeTaskPriority(<xsl:value-of select="$id" />, this.value, taskAfterAction)
                                    </xsl:attribute>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                </xsl:otherwise>
                            </xsl:choose>
                            <xsl:if test="id = $priorityId">
                                <xsl:attribute name="checked">checked</xsl:attribute>
                            </xsl:if>
                        </input>

                        <label for="priority_{$id}_{id}" id="label_priority_{id}">
                            <input type="hidden" />
                        </label>
                    </xsl:for-each>
                </div>

                <!--<div class="row">-->
                    <!--<xsl:if test="done = 0">-->
                        <!--<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">-->
                            <!--&lt;!&ndash;<a href="#" class="action append_done task_append_done" data-task_id="{id}" title="Закрыть задачу"><input type="hidden" /></a>&ndash;&gt;-->
                            <!--<a class="action append_done" onclick="markAsDone({id}, taskAfterAction)" title="Закрыть задачу"><input type="hidden" /></a>-->
                        <!--</div>-->
                    <!--</xsl:if>-->
                    <!--<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">-->
                        <!--&lt;!&ndash;<a data-task_id="{$id}" class="action comment task_add_note" data-table_type="{/root/table_name}" title="Добавить комментарий"><input type="hidden" /></a>&ndash;&gt;-->
                        <!--<a class="action comment task_add_note" title="Добавить комментарий" onclick="addTaskNotePopup({id})"><input type="hidden" /></a>-->
                    <!--</div>-->
                    <!--<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">-->
                        <!--&lt;!&ndash;<a href="#" class="action associate associate" title="Привязать к клиенту" data-task_id="{$id}"><input type="hidden" /></a>&ndash;&gt;-->
                        <!--<a href="#" class="action associate" title="Привязать к клиенту" onclick="assignmentTaskPopup({id})"><input type="hidden" /></a>-->
                    <!--</div>-->
                    <!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">-->
                        <!--<xsl:if test="associate != 0">-->
                            <!--<xsl:variable name="fio">-->
                                <!--<xsl:value-of select="user/surname" />-->
                                <!--<xsl:text> </xsl:text>-->
                                <!--<xsl:value-of select="user/name" />-->
                            <!--</xsl:variable>-->

                            <!--<a class="user-icon" title="{$fio}" href="{//wwwroot}/balance?userid={user/id}" target="_blank"><input type="hidden" /></a>-->
                        <!--</xsl:if>-->
                        <!--<input type="hidden" />-->
                    <!--</div>-->
                <!--</div>-->

                <div class="row comments">
                    <xsl:apply-templates select="comments/task_note" />
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