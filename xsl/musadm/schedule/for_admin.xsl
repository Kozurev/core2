<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <!--<table>-->
            <!--<tr>-->
                <!--<xsl:for-each select="class">-->
                    <!--<th colspan="3">КЛАСС <xsl:value-of select="position()"/></th>-->
                <!--</xsl:for-each>-->
            <!--</tr>-->

            <!--<tr>-->
                <!--<xsl:for-each select="class">-->
                    <!--<th>Время</th>-->
                    <!--<th>Основной</th>-->
                    <!--<th>Текущий</th>-->
                <!--</xsl:for-each>-->
            <!--</tr>-->

            <!--<xsl:for-each select="class">-->
                 <!--<tr>-->

                 <!--</tr>-->
            <!--</xsl:for-each>-->
        <!--</table>-->

        <div class="row" style="margin-top: 20px">
            <xsl:for-each select="class">
                <div class="col-lg-4">
                    <div class="col-lg-12 head">
                        КЛАСС <xsl:value-of select="position()" />
                    </div>
                    <div class="col-lg-4 head">Время</div>
                    <div class="col-lg-4 head">Основной</div>
                    <div class="col-lg-4 head">Текущий</div>
                    <xsl:variable name="curent_class" select="position()" />
                    <xsl:for-each select="//schedule_lesson[class_id = $curent_class]">
                        <div class="col-lg-4 body">
                            <xsl:value-of select="time_from" /><br/>
                            <xsl:value-of select="time_to" />
                        </div>

                        <div class="col-lg-4 body">
                            <xsl:value-of select="teacher" /> <br/>
                            <xsl:value-of select="client" />
                        </div>

                        <div class="col-lg-4 body">
                            <xsl:value-of select="teacher" /> <br/>
                            <xsl:value-of select="client" />
                        </div>
                    </xsl:for-each>
                </div>

            </xsl:for-each>
        </div>
    </xsl:template>


    <xsl:template match="class">
        <th colspan="3">КЛАСС <xsl:value-of select="position()"/></th>
    </xsl:template>

</xsl:stylesheet>