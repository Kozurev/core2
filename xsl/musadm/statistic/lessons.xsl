<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-12">
            <h3>Занятия</h3>
            <table class="simple-little-table statistic_lessons_table">
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
            </table>
        </div>
    </xsl:template>


</xsl:stylesheet>