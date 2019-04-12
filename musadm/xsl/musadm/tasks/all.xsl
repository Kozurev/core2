<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="task.xsl" />

    <xsl:template match="root">

        <input type='hidden' id='taskAfterAction' value='{taskAfterAction}' />

        <section class="section-bordered center">
            <xsl:if test="periods = 1">
                <div class="row finances-calendar">
                    <div class="right">
                        <h4>Период с:</h4>
                    </div>

                    <div>
                        <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                    </div>

                    <div class="right">
                        <h4>по:</h4>
                    </div>

                    <div>
                        <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                    </div>

                    <div>
                        <a class="btn btn-red"
                           onclick="refreshTasksTable($('input[name=date_from]').val(), $('input[name=date_to]').val())">
                            Показать
                        </a>
                    </div>
                </div>
            </xsl:if>

            <xsl:if test="buttons-panel = 1">
                <div class="row buttons-panel">
                    <xsl:choose>
                        <xsl:when test="periods = 1">
                            <div>
                                <a class="btn btn-red" onclick="newTaskPopup(0, 'refreshTasksTable')">Добавить задачу</a>
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <div>
                                <a class="btn btn-red" onclick="newTaskPopup(0, 'refreshTasksTable')">Добавить задачу</a>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
            </xsl:if>
        </section>

        <section class="cards-section text-center tasks-section">
            <div id="cards-wrapper" class="cards-wrapper row">
                <xsl:apply-templates select="task[done = 0]" />
                <xsl:apply-templates select="task[done = 1]" />
            </div>
        </section>
    </xsl:template>





</xsl:stylesheet>