<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="table-responsive">
            <table class="table table-bordered teacher_table">
                <tr class="header">
                    <th>Дата</th>
                    <th>Время</th>
                    <th>Преподаватель</th>
                    <td>Результат</td>
                    <th>Действия</th>
                </tr>
                <xsl:apply-templates select="lesson" />
            </table>
        </div>
    </xsl:template>


    <xsl:template match="lesson">
        <tr>
            <td><xsl:value-of select="/root/date" /></td>

            <td>
                <xsl:value-of select="time_from" />
                <xsl:text> - </xsl:text>
                <xsl:value-of select="time_to" />
            </td>

            <td>
                <xsl:value-of select="../user/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="../user/name" />
            </td>

            <td class="left">
                <ul>
                    <xsl:choose>
                        <xsl:when test="type_id = 3">
                            <xsl:variable name="lidId" select="client_id" />
                            <xsl:variable name="isReported">
                                <xsl:value-of select="count(report)" />
                            </xsl:variable>

                            <input type="checkbox" name="attendance_{$lidId}" id="attendance_{$lidId}">
                                <xsl:if test="$isReported = 1">
                                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                                </xsl:if>
                                <xsl:if test="report/attendance = 1">
                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                </xsl:if>
                            </input>
                            <xsl:text>   </xsl:text>
                            <label for="attendance_{$lidId}">
                                Консультация
                                <xsl:if test="client_id != 0">
                                    <xsl:value-of select="client_id" />
                                    <xsl:if test="client/name != '' or client/surname != '' or client/number != ''">
                                        <xsl:text> (</xsl:text>
                                        <xsl:if test="client/surname != ''">
                                            <xsl:value-of select="client/surname" />
                                            <xsl:text> </xsl:text>
                                        </xsl:if>
                                        <xsl:if test="client/name != ''">
                                            <xsl:value-of select="client/name" />
                                        </xsl:if>
                                        <xsl:text>)</xsl:text>
                                    </xsl:if>
                                </xsl:if>
                            </label>
                            <xsl:if test="$isReported = 0 and $lidId > 0">
                                <input type="text" class="form-control" placeholder="Примечание" name="note" data-lidid="{$lidId}"/>
                            </xsl:if>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:apply-templates select="client" />
                        </xsl:otherwise>
                    </xsl:choose>
                </ul>
            </td>

            <input type="hidden" name="date" value="{//real_date}"/>
            <input type="hidden" name="typeId" value="{type_id}" />
            <xsl:choose>
                <xsl:when test="oldid != ''">
                    <input type="hidden" name="lessonId" value="{oldid}"/>
                </xsl:when>
                <xsl:otherwise>
                    <input type="hidden" name="lessonId" value="{id}"/>
                </xsl:otherwise>
            </xsl:choose>

            <td>
                <xsl:if test="is_reported = 0 and /root/access_report_create = 1">
                    <a class="action save send_report" title="Сохранить отчет о проведении занятия"></a>
                </xsl:if>
                <xsl:if test="is_reported = 1 and /root/access_report_delete = 1">
                    <a class="action unarchive delete_report" title="Отменить отправку отчета"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="client">
        <xsl:choose>
            <xsl:when test="title != ''">
                <li>
                    <input type="checkbox" id="group_{id}" name="group" value="{id}" class="group-checkbox">
                        <xsl:if test="../report/id">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if>
                        <xsl:if test="../report/attendance = 1">
                            <xsl:attribute name="checked">checked</xsl:attribute>
                        </xsl:if>
                    </input>
                    <label for="group_{id}" class="group-label">
                        <xsl:value-of select="title" />
                    </label>
                    <a href="#" class="show-group-users">(состав)</a>
                    <ul class="group-list">
                        <xsl:apply-templates select="client" />
                    </ul>
                </li>
            </xsl:when>
            <xsl:otherwise>
                <li>
                    <input type="checkbox" name="attendance_{id}" id="attendance_{id}">
                        <xsl:if test="report/id or attendance/id">
                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                        </xsl:if>
                        <xsl:if test="attendance/attendance = 1">
                            <xsl:attribute name="checked">checked</xsl:attribute>
                        </xsl:if>
                    </input>
                    <xsl:text>   </xsl:text>
                    <label for="attendance_{id}">
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </label>
                </li>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>