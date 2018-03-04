<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


	<xsl:template match="root">
		<div class="in_main">
			<h3 class="main_title">
				<xsl:value-of select="title" />
			</h3>
			<table class="table">
				<xsl:apply-templates select="admin_form_modelname" />
				<xsl:apply-templates select="admin_form" />
			</table>
		</div>
	</xsl:template>


	<xsl:template match="admin_form_modelname">
		<tr>
			<td><xsl:value-of select="id" /></td>
			<td>
				<a class="link" href="/admin?menuTab=Form&amp;menuAction=show&amp;parent_id={id}">
					<xsl:value-of select="model_name" />
				</a>
			</td>

			<td>
				<a href="/admin" data-model_name="Admin_Form_Model" data-model_id="{id}" class="delete">
				<img 
					src="/templates/template3/images/delete.ico" 
					class="delete_icon"/>
				</a>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="admin_form">
		<tr>
			<td><xsl:value-of select="id" /></td>
			<td><xsl:value-of select="title" /></td>
			<td><xsl:value-of select="var_name" /></td>
			<td>
				<input type="checkbox" class="activeCheckbox">
					<xsl:attribute name="model_name">Admin_Form</xsl:attribute>
					<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
					<xsl:if test="active = 1">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
				</input>
			</td>

			<td>
				<a href="/admin" data-model_name="Admin_Form" data-model_id="{id}" class="delete">
				<img 
					src="/templates/template3/images/delete.ico" 
					class="delete_icon"/>
				</a>
			</td>
		</tr>
	</xsl:template>



</xsl:stylesheet>