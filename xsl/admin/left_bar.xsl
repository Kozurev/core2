<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


<xsl:template match="root">
  <div class="col-lg-3 left_bar">
	  <ul id="accordion" class="accordion">
		  <xsl:apply-templates select="admin_menu[parent_id = 0]" />
	  </ul>
  </div>
</xsl:template>


<xsl:template match="admin_menu">
	<li>
		<div class="left_link">
			<i class="fa fa-globe"></i>
				<xsl:value-of select="title" />
			<i class="fa fa-chevron-down"></i>
		</div>
		<xsl:variable name="id" select="id" />
		<ul class="submenu">
			<xsl:for-each select="//admin_menu[parent_id = $id]">
				<li>
					<a href="admin?menuTab={model}&amp;menuAction=show" class="link">
						<xsl:value-of select="title" />
					</a>
				</li>
			</xsl:for-each>
		</ul>
	</li>
</xsl:template>


</xsl:stylesheet>