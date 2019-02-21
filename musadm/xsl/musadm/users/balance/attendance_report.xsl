<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-attendance section-bordered">
            <h3>История занятий</h3>

            <xsl:choose>
                <xsl:when test="count(schedule_lesson_report) = 0">
                    <p>История посещений пуста</p>
                </xsl:when>
                <xsl:otherwise>
                    <div class="balance-lessons table-responsive">
                        <table class="table table-bordered">
                            <tr class="header">
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Статус</th>
                                <th>Преподаватель</th>
                                <xsl:if test="is_director = 1">
                                    <th colspan="2">Финансы</th>
                                </xsl:if>
                            </tr>
                            <xsl:apply-templates select="schedule_lesson_report" />
                        </table>
                    </div>

                    <p>Всего занятий за данный период: <xsl:value-of select="count(schedule_lesson_report)" /></p>
                    <p>Из них явки/неявки:
                        <xsl:value-of select="count(schedule_lesson_report[attendance = 1])" />
                        <xsl:text>/</xsl:text>
                        <xsl:value-of select="count(schedule_lesson_report[attendance = 0])" />
                    </p>
                </xsl:otherwise>
            </xsl:choose>

        </section>
    </xsl:template>


    <xsl:template match="schedule_lesson_report">
        <tr>
            <td><xsl:value-of select="date" /></td>

            <td>
                <xsl:value-of select="time_from" />
                <xsl:text> - </xsl:text>
                <xsl:value-of select="time_to" />
            </td>

            <td>
                <xsl:choose>
                    <xsl:when test="attendance = 1">
                        Присутствовал(а)
                    </xsl:when>
                    <xsl:otherwise>
                        Отсутствовал(а)
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <td>
                <xsl:choose>
                    <xsl:when test="surname != '' and name != ''">
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </xsl:when>
                    <xsl:otherwise>
                        Пользователь был удален
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <xsl:if test="//is_director = 1">
                <td>
                    <xsl:value-of select="client_rate" />
                    / <xsl:value-of select="teacher_rate" />
                    / <xsl:value-of select="total_rate" />
                </td>
                <td class="center">
                    <a class="action edit edit_teacher_report" data-reportid="{id}"></a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>