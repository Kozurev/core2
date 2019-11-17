<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="section-bordered">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-lg-offset-4 col-md-offset-3 col-sm-offset-0">
                    <div class="table-responsive">
                        <table class="table">
                            <xsl:choose>
                                <xsl:when test="count(schedule_absent) = 0">
                                    <h4 class="center">Предстоящих периодов отсутствия не найдено</h4>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates select="schedule_absent" />
                                </xsl:otherwise>
                            </xsl:choose>
                            <tr>
                                <td colspan="3" class="center">
                                    <a style="width: auto" class="btn btn-green" onclick="getScheduleAbsentPopup({userId}, 1, getCurrentDate(), '')">
                                        Создать период отсутствия
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </xsl:template>

    <xsl:template match="schedule_absent">
        <tr>
            <td class="va-middle">
                <xsl:value-of select="refactoredDateFrom" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="refactoredTimeFrom" />
            </td>
            <td class="va-middle">
                <xsl:value-of select="refactoredDateTo" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="refactoredTimeTo" />
            </td>
            <td class="right">
                <a class="action edit" onclick="getScheduleAbsentPopup('', '', '', {id})"></a>
                <a class="action delete" onclick="deleteScheduleAbsent({id}, refreshSchedule)"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>