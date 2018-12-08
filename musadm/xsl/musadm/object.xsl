<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <xsl:apply-templates select="task" />
        <xsl:apply-templates select="lid" />
        <xsl:apply-templates select="certificate" />
    </xsl:template>


    <xsl:template match="task">
        <h3>Задача №<xsl:value-of select="id" /> на <xsl:value-of select="date" /></h3>
        <div class="tasks-comments">
            <xsl:apply-templates select="//note" />
        </div>
    </xsl:template>


    <xsl:template match="lid">
        <h3>Лид №<xsl:value-of select="id" /> на <xsl:value-of select="control_date" /></h3>

        <xsl:if test="surname != '' or name != ''">
            <h3>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
                (<xsl:value-of select="status" />)
            </h3>
        </xsl:if>

        <xsl:if test="number != ''">
            <p><xsl:value-of select="number" /></p>
        </xsl:if>

        <xsl:if test="vk">
            <p><xsl:value-of select="vk" /></p>
        </xsl:if>

        <xsl:if test="source != ''">
            <p><xsl:value-of select="source" /></p>
        </xsl:if>

        <div class="tasks-comments">
            <xsl:apply-templates select="//note" />
        </div>
    </xsl:template>


    <xsl:template match="certificate">
        <!--<xsl:value-of select="count(//note)" />-->
        <xsl:variable name="id" select="id" />
        <h3>Сертификат №<xsl:value-of select="number" /> продан: <xsl:value-of select="sell_date" /> активен до: <xsl:value-of select="active_to" /></h3>
        <div class="tasks-comments">
            <xsl:apply-templates select="/root/note" />
        </div>
    </xsl:template>


    <xsl:template match="note">
        <div class="block">
            <div class="comment_header">
                <div class="author">
                    <xsl:choose>
                        <xsl:when test="author_id = 0">
                            Система
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
                <div class="date">
                    <xsl:value-of select="date" />
                </div>
            </div>

            <div class="comment_body">
                <p><xsl:value-of select="text" /></p>
            </div>
        </div>
    </xsl:template>


</xsl:stylesheet>