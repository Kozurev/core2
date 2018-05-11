<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <table class="table teacher_table">
            <tr>
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
            <form>
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
                    <xsl:apply-templates select="client" />
                </td>

                <td>
                    <input type="checkbox" name="attendance" >
                        <xsl:if test="disabled = 1">
                            <xsl:attribute name="disabled">
                                disabled
                            </xsl:attribute>
                        </xsl:if>
                    </input>
                </td>

                <input type="hidden" name="teacherId" value="{../user/id}" />

                <input type="hidden" name="clientId" >
                    <xsl:attribute name="value">
                        <xsl:choose>
                            <xsl:when test="client/title != ''">
                                0
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="client/id" />
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                </input>

                <input type="hidden" name="groupId" >
                    <xsl:attribute name="value">
                        <xsl:choose>
                            <xsl:when test="client/title != ''">
                                <xsl:value-of select="client/id" />
                            </xsl:when>
                            <xsl:otherwise>
                                0
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                </input>

                <td>
                    <button class="btn send_report" >
                        <xsl:if test="disabled = 1">
                            <xsl:attribute name="disabled">
                                disabled
                            </xsl:attribute>
                        </xsl:if>
                        Отправить данные
                    </button>
                </td>
            </form>
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