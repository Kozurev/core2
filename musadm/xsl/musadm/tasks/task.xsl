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
                    <!--Дата контроля-->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <input type="date" class="form-control task_date" data-taskid="{id}" value="{date}" />
                    </div>

                    <!--Филлиал-->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <select name="area" class="form-control task_area" data-taskid="{id}">
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

                <div class="row center">
                    <xsl:for-each select="//task_priority">
                        <input type="radio" name="priority_{$id}" data-type="task_priority" value="{id}" id="priority_{$id}_{id}" data-taskid="{$id}" data-card-size="{/root/card-size}" >
                            <xsl:if test="id = $priorityId">
                                <xsl:attribute name="checked">checked</xsl:attribute>
                            </xsl:if>
                        </input>

                        <label for="priority_{$id}_{id}" id="label_priority_{id}">
                            <input type="hidden" />
                        </label>
                    </xsl:for-each>
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