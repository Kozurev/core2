<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>

  <xsl:template match="/">
    <h2>Подключаемый шаблон</h2>
    <xsl:apply-templates select="root"/>
  </xsl:template>
  
  <xsl:template match="root">
    <xsl:apply-templates select="structure_item"/>
    <xsl:apply-templates select="structure"/>
  </xsl:template>
  
  
  <xsl:template match="structure_item">
    <xsl:variable name="id" select="id" />
    <h3>Номер объекта: <xsl:value-of select="$id" /> </h3>
    <h3>Название объекта: <xsl:value-of select="title" /> </h3>
    <xsl:apply-templates select="../property_value[object_id = $id][model_name = 'Structure_Item']" />
    <br/>
  </xsl:template>


  <xsl:template match="structure">
    <xsl:variable name="id" select="id" />
    <h3>Номер структуры: <xsl:value-of select="$id" /> </h3>
    <h3>Название структуры: <xsl:value-of select="title" /> </h3>
    <xsl:apply-templates select="../property_value[object_id = $id][model_name = 'Structure']" />
    <br/>
  </xsl:template>


  <xsl:template match="property_value">

    <xsl:if test="position() != 1">
      <xsl:text>, </xsl:text>
    </xsl:if>

    <xsl:value-of select="value" />
  </xsl:template>


</xsl:stylesheet>