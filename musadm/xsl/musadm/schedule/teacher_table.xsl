<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table class="table table-bordered teacher_table">
            <tr class="header">
                <th>Дата</th>
                <th>Время</th>
                <th>Преподаватель</th>
                <th>Ученик / Группа</th>
                <th>Отметка о явке</th>
                <th>Отправить данные</th>
            </tr>
            <xsl:apply-templates select="lesson" />
        </table>
    </xsl:template>


    <xsl:template match="lesson">
        <tr>
            <td><xsl:value-of select="/root/date" /></td>

            <td>
                <xsl:value-of select="time_from" /><br/>
                <xsl:value-of select="time_to" />
            </td>

            <td>
                <xsl:value-of select="../user/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="../user/name" />
            </td>

            <td>
                <xsl:choose>
                    <xsl:when test="type_id = 3">
                        Консультация
                        <xsl:if test="client_id != 0">
                            <xsl:value-of select="client_id" />
                        </xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="client" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <td>
                <input type="checkbox" name="attendance" >
                    <xsl:if test="count(report/id) != 0">
                        <xsl:attribute name="disabled">
                            disabled
                        </xsl:attribute>
                    </xsl:if>

                    <xsl:if test="report/attendance = 1">
                        <xsl:attribute name="checked" >
                            checked
                        </xsl:attribute>
                    </xsl:if>
                </input>
            </td>

            <input type="hidden" name="teacherId" value="{../user/id}" />
            <input type="hidden" name="typeId" value="{type_id}"/>
            <input type="hidden" name="clientId" value="{client_id}"/>
            <input type="hidden" name="date" value="{//real_date}"/>
            
            <xsl:choose>
                <xsl:when test="oldid != ''">
                    <input type="hidden" name="lessonId" value="{oldid}"/>
                </xsl:when>
                <xsl:otherwise>
                    <input type="hidden" name="lessonId" value="{id}"/>
                </xsl:otherwise>
            </xsl:choose>

            <input type="hidden" name="reportId" value="{report/id}" />
            <input type="hidden" name="lessonType" value="{lesson_type}" />

            <td>
                <button class="btn btn-green send_report" >
                    <xsl:if test="count(report/id) != 0">
                        <xsl:attribute name="disabled">
                            disabled
                        </xsl:attribute>
                    </xsl:if>
                    Отправить данные
                </button>

                <xsl:if test="count(report/id) != 0 and /root/is_admin = 1">
                    <button class="btn btn-danger delete_report">
                        Отменить
                    </button>
                </xsl:if>

            </td>
        </tr>
    </xsl:template>

    <xsl:template match="client">
        <xsl:choose>
            <xsl:when test="title != ''">
                <xsl:value-of select="title" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>