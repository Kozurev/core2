<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <h2 class="center">Сортировка лидов</h2>
        <xsl:variable name="selectedTeacherId" select="selectedTeacherId" />
        <xsl:if test="count(user) != 0">
            <h4>Преподаватель:</h4>
            <select style="width:50%"   class="form-control" id="lids_statistic_teacherId">
                <option value="0"> ... </option>
                <xsl:for-each select="user">
                    <!--<xsl:variable name="id" select="id" />-->
                    <option value="{id}">
                        <xsl:if test="id = $selectedTeacherId">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </option>
                </xsl:for-each>
            </select>
        </xsl:if>

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <h3 class="center">по дате созвона</h3>
            <table class="table table-bordered table-hover statistic_lids_table">
                <tr>
                    <th colspan="2">Лиды</th>
                </tr>
                <tr>
                    <td>Всего:</td>
                    <td><xsl:value-of select="total" /></td>
                </tr>
                <xsl:apply-templates select="status" />
            </table>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <h3 class="center">по расписанию</h3>

            <table class="table table-bordered table-hover statistic_lids_table">
                <tr>
                    <th colspan="2">Лиды</th>
                </tr>
                <tr>
                    <td>Всего:</td>
                    <td><xsl:value-of select="totalFromSchedule" /></td>
                </tr>
                <xsl:apply-templates select="statusSchedule" />
            </table>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
            <h3 class="center">по дате создания</h3>
            <table class="table table-bordered table-hover statistic_lids_table">
                <tr>
                    <th colspan="2">Лиды</th>
                </tr>
                <tr>
                    <td>Всего:</td>
                    <td><xsl:value-of select="totalFromComment" /></td>
                </tr>
                <xsl:apply-templates select="statusComment" />
            </table>
        </div>
    </xsl:template>


    <xsl:template match="status">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td>
                <xsl:value-of select="count" /> (<xsl:value-of select="percents" />%)
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="statusSchedule">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td>
                <xsl:value-of select="countSchedule" /> (<xsl:value-of select="percentsSchedule" />%)
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="statusComment">
        <tr>
            <td><xsl:value-of select="title" /></td>
            <td>
                <xsl:value-of select="statusComment" /> (<xsl:value-of select="percentsComment" />%)
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>