<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


    <xsl:template name="input">
        <xsl:param name="inp_type" />
        <xsl:param name="maxlength" />
        <xsl:param name="var_name" />
        <xsl:param name="value" />
        <xsl:param name="class" />
        <xsl:param name="required" />

        <input type="{$inp_type}" name="{$var_name}" class="form-control {$inp_type} {$class}">
            <xsl:if test="$maxlength != 0">
                <xsl:attribute name="maxlength">
                    <xsl:value-of select="$maxlength" />
                </xsl:attribute>
            </xsl:if>

            <xsl:if test="$required = '1'">
                <xsl:attribute name="required">
                    required
                </xsl:attribute>
            </xsl:if>

            <xsl:if test="$inp_type = 'checkbox'">
                <xsl:if test="value = 1">
                    <xsl:attribute name="checked">checked</xsl:attribute>
                </xsl:if>
            </xsl:if>
            <xsl:if test="$inp_type != 'checkbox'">
                <xsl:attribute name="value">
                    <xsl:value-of select="$value" />
                </xsl:attribute>
            </xsl:if>
        </input>

    </xsl:template>


    <xsl:template name="textarea">
        <xsl:param name="maxlength" />
        <xsl:param name="var_name"/>
        <xsl:param name="value" />
        <xsl:param name="addClass" />
        <xsl:param name="required" />

        <textarea name="{$var_name}" maxlength="{$maxlength}" class="textarea {$addClass}">

            <xsl:if test="$required != ''">
                <xsl:attribute name="required">
                    required
                </xsl:attribute>
            </xsl:if>

            <xsl:value-of select="$value" />
        </textarea>
    </xsl:template>


    <xsl:template name="select">
        <xsl:param name="var_name" />
        <xsl:param name="value" />
        <xsl:param name="addClass" />

        <select name="{$var_name}" class="form-control {$addClass}">
            <option value="0">...</option>
            <xsl:for-each select="item">
                <xsl:variable name="id" select="id" />
                <option value="{$id}">
                    <xsl:if test="$id = $value">
                        <xsl:attribute name="selected">TRUE</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="title"/>
                    <xsl:value-of select="value"/>
                </option>
            </xsl:for-each>
        </select>
    </xsl:template>


</xsl:stylesheet>