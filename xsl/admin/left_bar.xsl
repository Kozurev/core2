<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


<xsl:template match="root">
  <div class="col-lg-3 left_bar">
    <xsl:apply-templates select="admin_menu" />
  </div>
</xsl:template>


<xsl:template match="admin_menu">
	<a href="/admin?menuTab={model}&amp;menuAction=show" class="link">
		<div class="item">
		   	<span class="text">
		    	<xsl:value-of select="title" />
		    </span>
		</div>
  	</a>
</xsl:template>


</xsl:stylesheet>