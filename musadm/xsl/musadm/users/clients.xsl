<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <xsl:if test="user/active = 1">
            <div style="text-align: right; margin: 20px 0px">
                <a href="#" class="btn btn-primary user_create" data-usergroup="5">Создать пользователя</a>
            </div>
        </xsl:if>

        <table id="sortingTable" class="table table-striped">
            <thead>
                <tr class="header">
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Телефон</th>
                    <th>Баланс</th>
                    <th>Кол-во индив. занятий</th>
                    <th>Кол-во групп. занятий</th>
                    <th>Студия</th>
                    <th>Действия</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="user" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="user">

        <xsl:variable name="class" >
            <xsl:choose>
                <xsl:when test="property_value[property_id = 13]/value &lt; 0 or property_value[property_id = 14]/value &lt; 0">
                    negative
                </xsl:when>
                <xsl:when test="property_value[property_id = 13]/value &gt; 1 or property_value[property_id = 14]/value &gt; 1">
                    positive
                </xsl:when>
                <xsl:otherwise>
                    neutral
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr>
            <td>
                <a href="balance?userid={id}">
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
                <xsl:value-of select="phone_number" /><br/>
                <xsl:value-of select="property_value[property_id = 16]/value" />
            </td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 12]/value" /></td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 13]/value" /></td>
            <td class="{$class}"><xsl:value-of select="property_value[property_id = 14]/value" /></td>
            <td><xsl:value-of select="property_value[property_id = 15]/value" /></td>

            <xsl:if test="//table_type = 'active'">
                <td>
                    <a class="action add_payment user_add_payment" href="#" data-userid="{id}"></a>
                    <a class="action edit user_edit"        href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                    <a class="action archive user_archive"     href="#" data-userid="{id}"></a>
                </td>
            </xsl:if>

            <xsl:if test="//table_type = 'archive'">
                <td>
                    <a class="action unarchive user_unarchive"   href="#" data-userid="{id}"></a>
                    <a class="action delete user_delete"      href="#" data-model_id="{id}" data-model_name="User"></a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>

</xsl:stylesheet>