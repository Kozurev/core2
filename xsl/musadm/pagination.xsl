<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="pagination">
        <ul class="pagination pagination-sm justify-content-center">
            <!--Первая страница-->
            <li class="page-item">
                <a class="page-link" href="#">
                    Первая
                </a>
            </li>

            <!--Указатель на предыдущую страницу-->
            <li class="page-item">
                <xsl:if test="currentPage = 1">
                    <xsl:attribute name="class">
                        page-item disabled
                    </xsl:attribute>
                </xsl:if>
                <a class="page-link" href="#">
                    <span aria-hidden="true">←</span>
                </a>
            </li>
            <!--Предыдущая страница-->
            <xsl:if test="prevPage != 0">
                <li class="page-item">
                    <a class="page-link" href="#">
                        <xsl:value-of select="prevPage" />
                    </a>
                </li>
            </xsl:if>

            <!--Текущая страница-->
            <li class="page-item active">
                <a class="page-link" href="#">
                    <xsl:value-of select="currentPage" />
                </a>
            </li>

            <!--Следующая страница-->
            <xsl:if test="nextPage != 0">
                <li class="page-item">
                    <a class="page-link" href="#">
                        <xsl:value-of select="nextPage" />
                    </a>
                </li>
            </xsl:if>

            <!--Указатель на следующую страницу-->
            <li class="page-item">
                <xsl:if test="currentPage = countPages">
                    <xsl:attribute name="class">
                        page-item disabled
                    </xsl:attribute>
                </xsl:if>
                <a class="page-link" href="#">
                    <span aria-hidden="true">→</span>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" href="#">
                    Последняя
                </a>
            </li>
        </ul>
    </xsl:template>

</xsl:stylesheet>