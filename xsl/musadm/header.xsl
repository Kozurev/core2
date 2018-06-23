<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="branding">
            <h1 class="logo">
                <a href="#">
                    <span aria-hidden="true" class="icon_documents_alt icon"></span>
                    <span class="text-highlight"><xsl:value-of select="title-first" /></span>
                    <span class="text-bold">
                    	<xsl:text> </xsl:text> 
                    	<xsl:value-of select="title-second" />
                    </span>
                </a>
            </h1>
        </div><!--//branding-->
        <ol class="breadcrumb">
            <li><a href="#">Главная</a></li>
            <xsl:apply-templates select="breadcumb" />
        </ol>
	</xsl:template>


	<xsl:template match="breadcumb">
		<xsl:choose>
			<xsl:when test="active = 1">
				<li class="active"><xsl:value-of select="title" /></li>
			</xsl:when>
			<xsl:otherwise>
				<li><a href="{href}"><xsl:value-of select="title" /></a></li>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>


</xsl:stylesheet>