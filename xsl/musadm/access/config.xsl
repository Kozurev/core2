<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <xsl:choose>
            <xsl:when test="count(//core_access_group) = 0">
                <h2>Ошибка: группа с id <xsl:value-of select="//group_id" /> не существует</h2>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="core_access_group" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="core_access_group">
        <h3><xsl:value-of select="title" /></h3>
        <h5><xsl:value-of select="description" /></h5>

        <table class="table table-striped">
            <tr>
                <th class="header">Название возможности</th>
                <th class="header">По умолчанию</th>
                <th class="header">Разрешено</th>
                <th class="header">Запрещено</th>
            </tr>
            <xsl:apply-templates select="capability" />
        </table>
    </xsl:template>

    <xsl:template match="capability">
        <xsl:variable name="currentName" select="name" />
        <xsl:variable name="groupId" select="/root/core_access_group/id" />

        <tr>
            <td>
                <xsl:value-of select="title" />
            </td>
            <td>
                <xsl:if test="/root/core_access_group/parent_id != 0">
                    <input type="radio" name="access_{id}" id="access_{id}_1" value="-1">
                        <xsl:if test="group_id != /root/group_id">
                            <xsl:attribute name="checked">checked</xsl:attribute>
                        </xsl:if>
                    </input>
                    <label for="access_{id}_1" class="radioBlack" onclick="Access.capabilityAsParent({$groupId}, '{name}')">
                        <input type="hidden"/>
                    </label>

                    <xsl:choose>
                        <xsl:when test="/root/parentGroup/capability[name = $currentName]/access = 1">
                            <span class="green">(Разрешено)</span>
                        </xsl:when>
                        <xsl:otherwise>
                            <span class="red">(Запрещено)</span>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:if>
            </td>
            <td>
                <input type="radio" name="access_{id}" id="access_{id}_2" value="1">
                    <xsl:if test="access = 1 and group_id = /root/group_id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="access_{id}_2" class="radioGreen" onclick="Access.capabilityAppend({$groupId}, '{name}')">
                    <input type="hidden"/>
                </label>
            </td>
            <td>
                <input type="radio" name="access_{id}" id="access_{id}_3" value="0">
                    <xsl:if test="access = 0 and group_id = /root/group_id">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="access_{id}_3" class="radioRed" onclick="Access.capabilityForbidden({$groupId}, '{name}')">
                    <input type="hidden"/>
                </label>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>