<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../tasks/task.xsl" />

    <xsl:template match="root">

        <section class="user-events">
            <input type="hidden" id='taskAfterAction' value='{afterTaskAction}' />

            <xsl:if test="user/group_id = 5">
                <div class="row">
                    <div class="col-md-4 col-xs-12 right">
                        <h4>Добавить комментарий</h4>
                    </div>
                    <div class="col-md-6 col-xs-8 left">
                        <input class="form-control" id="user_comment" placeholder="Комментарий" />
                    </div>
                    <div class="col-md-2 col-xs-4">
                        <a class="btn btn-orange" id="user_comment_save" href="#" data-userid="{user/id}">Сохранить</a>
                    </div>
                </div>
            </xsl:if>

            <xsl:variable name="role_name">
                <xsl:if test="user/group_id = 5">клиентом</xsl:if>
                <xsl:if test="user/group_id = 2">менеджером</xsl:if>
            </xsl:variable>

            <xsl:choose>
                <xsl:when test="count(event) = 0">
                    <h3>Не найдено ни одного события, связанного с <xsl:value-of select="$role_name" /></h3>
                </xsl:when>
                <xsl:otherwise>
                    <h3>События, связанные с <xsl:value-of select="$role_name" />:</h3>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:if test="user/group_id = 2">
                <div class="row finances-calendar">
                    <div class="right">
                        <h4>Период с:</h4>
                    </div>

                    <div>
                        <input type="date" class="form-control" name="event_date_from" value="{date_from}"/>
                    </div>

                    <div class="right">
                        <h4>по:</h4>
                    </div>

                    <div>
                        <input type="date" class="form-control" name="event_date_to" value="{date_to}"/>
                    </div>

                    <div>
                        <a class="btn btn-green events_show">Показать</a>
                    </div>
                </div>
            </xsl:if>

            <div class="tasks-comments">
                <div>
                    <xsl:if test="user/group_id = 5">
                        <xsl:attribute name="class">balance-payments tab</xsl:attribute>
                    </xsl:if>
                    <xsl:apply-templates select="event" />
                </div>
                <xsl:if test="enable_load_button = 1">
                    <div class="center">
                        <button class="btn btn-orange events_load_more" data-limit="{limit}">
                            Загрузить ещё
                        </button>
                    </div>
                </xsl:if>
            </div>
        </section>
    </xsl:template>


    <xsl:template match="event">

        <xsl:choose>
            <!--Если это задача, привязанная к клиенту-->
            <xsl:when test="id = ''">
                <xsl:variable name="id" select="task/id" />
                <section class="cards-section text-center tasks-section tasks">
                    <div id="cards-wrapper" class="cards-wrapper row">
                        <xsl:apply-templates select="task" />
                    </div>
                </section>
            </xsl:when>
            <!--Если это просто событие-->
            <xsl:otherwise>
                <div class="block">
                    <div class="comment_header">
                        <div class="author">
                            <xsl:choose>
                                <xsl:when test="author_id = 0">
                                    Система
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="author_fio" />
                                </xsl:otherwise>
                            </xsl:choose>
                        </div>
                        <div class="date">
                            <xsl:value-of select="date" />
                        </div>
                    </div>

                    <div class="comment_body">
                        <p><xsl:value-of select="text" disable-output-escaping="yes" /></p>
                    </div>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


</xsl:stylesheet>