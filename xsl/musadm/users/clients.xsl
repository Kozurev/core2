<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <style>
            .positive {
                background-color:palegreen !important;
            }
            .negative {
                background-color:indianred !important;
            }
            .neutral {
                background-color:lightyellow !important;
            }
        </style>

        <table id="sortingTable" class="tablesorter">
            <thead>
                <tr>
                    <th class="header">Фамилия</th>
                    <th class="header">Имя</th>
                    <th>Телефон</th>
                    <th class="header">Баланс</th>
                    <th class="header">Кол-во индив. занятий</th>
                    <th class="header">Кол-во групп. занятий</th>
                    <th class="header">Студия</th>
                    <th>Действия</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="user" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="user">
        <tr>

            <xsl:variable name="class" >
                <xsl:choose>
                    <xsl:when test="property_value[property_id = 13]/value &lt; 0 or property_value[property_id = 14]/value &lt; 0">
                        negative
                    </xsl:when>
                    <xsl:when test="property_value[property_id = 13]/value &gt; 2 or property_value[property_id = 14]/value &gt; 2">
                        positive
                    </xsl:when>
                    <xsl:otherwise>
                        neutral
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>

            <td>
                <a href="../?userid={id}">
                    <xsl:value-of select="surname" />
                </a>
                <!--Анкета (соглашение подписано)-->
                <span class="anketa">
                    <xsl:if test="property_value[property_id = 18]/value = '1'">
                        Д+
                    </xsl:if>
                </span>

                <!--Примечания-->
                <div class="notes">
                    <xsl:value-of select="property_value[property_id = 19]/value" />
                </div>
            </td>
            <td><xsl:value-of select="name" /></td>
            <td>
                <xsl:value-of select="phone_number" />
                <xsl:value-of select="property_value[property_id = 16]/value" />
            </td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 12]/value" /></td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 13]/value" /></td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 14]/value" /></td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 15]/value" /></td>
            <td></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>