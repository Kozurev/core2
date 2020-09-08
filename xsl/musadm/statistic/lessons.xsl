<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
<!--        <div class="col-lg-4">-->
            <!--<h3>Занятия</h3>-->
            <table class="table table-bordered table-hover statistic_lessons_table">
                <tr>
                    <th colspan="2">Занятия</th>
                </tr>
                <tr>
                    <td>Всего проведено занятий:</td>
                    <td><xsl:value-of select="total_count" /></td>
                </tr>

                <tr>
                    <td>Присутствовало:</td>
                    <td>
                        <xsl:value-of select="attendance_count" />
                        <xsl:text> (</xsl:text>
                        <xsl:value-of select="attendance_percent"/>
                        <xsl:text>%)</xsl:text>
                    </td>
                </tr>

                <tr>
                    <td>Дневной индекс</td>
                    <td>
                        <xsl:choose>
                            <xsl:when test="day_index != '-0'">
                                <xsl:value-of select="day_index" />
                            </xsl:when>
                            <xsl:otherwise>
                                Для корректного отображения дневного индекса необходимо укаать точный временной промежуток
                                либо указав даты "с" и "по" либо удалив временной промежуток полностью
                            </xsl:otherwise>
                        </xsl:choose>
                    </td>
                </tr>
            </table>
<!--        </div>-->
    </xsl:template>


</xsl:stylesheet>