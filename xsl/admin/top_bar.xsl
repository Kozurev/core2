<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


  <xsl:template match="user">
    <div class="top_bar">
    	<span>Вы авторизованы как: </span>
    	<span class="login"><xsl:value-of select="login" /></span> 

		<button class="btn btn-danger" >
			<a href="admin?disauthorize=1" >
				Выйти
			</a>
		</button>

    </div>
  </xsl:template>



</xsl:stylesheet>