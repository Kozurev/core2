<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="user">

        <xsl:variable name="balance">
            <xsl:choose>
                <xsl:when test="property_value[property_id = 12]/value">
                    <xsl:value-of select="property_value[property_id = 12]/value" />
                </xsl:when>
                <xsl:otherwise>
                    0
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="count_indiv">
            <xsl:choose>
                <xsl:when test="property_value[property_id = 13]/value">
                    <xsl:value-of select="property_value[property_id = 13]/value" />
                </xsl:when>
                <xsl:otherwise>
                    0
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="count_group">
            <xsl:choose>
                <xsl:when test="property_value[property_id = 14]/value">
                    <xsl:value-of select="property_value[property_id = 14]/value" />
                </xsl:when>
                <xsl:otherwise>
                    0
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="class" >
            <xsl:choose>
                <xsl:when test="$count_indiv &lt; 0 or $count_group &lt; 0">
                    negative
                </xsl:when>
                <xsl:when test="$count_indiv &gt; 1 or $count_group &gt; 1">
                    positive
                </xsl:when>
                <xsl:otherwise>
                    neutral
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="areaId" select="schedule_area_assignment/area_id" />


        <tr class="{$class}">
            <!--Фамилия-->
            <td>
                <!--<a href="/{/root/wwwroot}authorize?auth_as={id}">-->
                <a href="{/root/wwwroot}/balance/?userid={id}">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                </a>

                <!--Анкета (соглашение подписано)-->
                <xsl:if test="property_value[property_id = 18]/value = '1'">
                    <span class="contract" title="Соглашение подписано"><input type="hidden"/></span>
                </xsl:if>

                <!--Год рождения-->
                <xsl:if test="property_value[property_id = 28]/value != ''">
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="property_value[property_id = 28]/value" />
                    <xsl:text> г.р.</xsl:text>
                </xsl:if>

                <!--Поурочная оплата-->
                <xsl:if test="property_value[property_id = 32]/value = '1'">
                    <div class="notes">«Сменный график»</div>
                </xsl:if>

                <!--Примечания-->
                <div class="notes">
                    <xsl:value-of select="property_value[property_id = 19]/value" />
                </div>
            </td>

            <!--номер (номера) телефона-->
            <td>
                <xsl:value-of select="phone_number" /><br/>
                <xsl:value-of select="property_value[property_id = 16]/value" />
            </td>

            <!--Баланс-->
            <td>
                <xsl:value-of select="$balance" />
            </td>

            <td width="150px">
                <xsl:value-of select="$count_indiv" />
                <xsl:text> / </xsl:text>
                <xsl:value-of select="$count_group" />
            </td>

            <!--Продрлжительность урока-->
            <td>
                <xsl:value-of select="property_value[property_id = 17]/value" />
            </td>

            <!--Студия-->
            <td>
                <xsl:value-of select="/root/schedule_area[id = $areaId]/title" />
            </td>

            <!--Действия-->
            <xsl:if test="//table-type = 'active'">
                <td width="140px">
                    <a class="action add_payment user_add_payment" href="#" data-userid="{id}" title="Добавить платеж"></a>
                    <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}" title="Редактировать данные"></a>
                    <a class="action archive user_archive" href="#" data-userid="{id}" title="Переместить в архив"></a>
                </td>
            </xsl:if>

            <xsl:if test="//table-type = 'archive'">
                <td>
                    <a class="action unarchive user_unarchive" href="#" data-userid="{id}" title="Восстановить из архива"></a>
                    <a class="action delete user_delete" href="#" data-model_id="{id}" data-model_name="User" title="Безвозвратное удаление"></a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>

</xsl:stylesheet>