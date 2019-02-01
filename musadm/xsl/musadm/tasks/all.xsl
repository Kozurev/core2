<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="task.xsl" />

    <xsl:template match="root">

        <input type='hidden' id='taskAfterAction' value='{taskAfterAction}' />

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


        <xsl:if test="buttons-panel = 1">
            <div class="row buttons-panel">
                <xsl:choose>
                    <xsl:when test="periods = 1">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <a class="btn btn-red task_create">Добавить задачу</a>
                        </div>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="class" >row buttons-panel center</xsl:attribute>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <a class="btn btn-red task_create">Добавить задачу</a>
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
        </xsl:if>

        <section class="cards-section text-center tasks-section">
            <div id="cards-wrapper" class="cards-wrapper row">
                <xsl:apply-templates select="task[done = 0]" />
                <xsl:apply-templates select="task[done = 1]" />
            </div>
        </section>
    </xsl:template>





</xsl:stylesheet>