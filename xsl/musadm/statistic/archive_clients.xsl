<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-4">
            <!--<h3>Выплаты преподавателям</h3>-->
            <table class="table table-bordered table-hover statistic_teacher_payments_table" >
                <tr>
                    <th colspan="2">Статистика по отвалу клиентов</th>
                </tr>
                <tr>
                    <td colspan="2">Число вернувшихся :<xsl:value-of select="count_comeback_client" /></td>
                </tr>
                <tr>
                    <td colspan="2">Число ушедших :<xsl:value-of select="count_leave_client" /></td>
                </tr>
                <tr>
                    <td colspan="2">Процент ушедших :<xsl:value-of select="count_percent_client" /></td>
                </tr>
                <tr>
                    <td colspan="2">Число новых :<xsl:value-of select="count_new_client" /></td>
                </tr>
                <tr>
                    <th>Причина отвала</th>
                    <th>Колличество </th>
                </tr>
                <xsl:apply-templates select="userActivityList"/>
            </table>
        </div>
    </xsl:template>




    <xsl:template match="userActivityList">

        <tr>
            <td><xsl:value-of select="value" /></td>
            <td><xsl:value-of select="count" /> </td>
        </tr>


    </xsl:template>
</xsl:stylesheet>