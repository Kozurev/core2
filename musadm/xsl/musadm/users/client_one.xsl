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


        <tr class="{$class}" id="user_{id}">
            <!--Фамилия-->
            <td>
                <span class="user__fio">
                    <a href="{/root/wwwroot}/balance/?userid={id}">
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </a>
                </span>

                <!--Анкета (соглашение подписано)-->
                <span class="add__18">
                    <xsl:if test="property_value[property_id = 18]/value = '1'">
                        <span class="contract" title="Соглашение подписано"><input type="hidden"/></span>
                    </xsl:if>
                    <input type="hidden"/>
                </span>

                <!--Год рождения-->
                <span class="user__birth">
                    <xsl:if test="property_value[property_id = 28]/value != ''">
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="property_value[property_id = 28]/value" />
                        <xsl:text> г.р.</xsl:text>
                    </xsl:if>
                </span>

                <!--Поурочная оплата-->
                <span class="add__32">
                    <xsl:if test="property_value[property_id = 32]/value = '1'">
                        <div class="notes">«Поурочно»</div>
                    </xsl:if>
                </span>

                <!--Примечания-->
                <span class="add__19">
                    <div class="notes">
                        <xsl:value-of select="property_value[property_id = 19]/value" />
                    </div>
                </span>
            </td>

            <!--номер (номера) телефона-->
            <td>
                <span class="user__phone">
                    <xsl:value-of select="phone_number" />
                </span>
                <br/>
                <span class="add__16">
                    <xsl:value-of select="property_value[property_id = 16]/value" />
                </span>
            </td>

            <!--Баланс-->
            <td>
                <span class="add__12">
                    <xsl:value-of select="$balance" />
                </span>
            </td>

            <td width="150px">
                <span class="add__13">
                    <xsl:value-of select="$count_indiv" />
                </span>
                <xsl:text> / </xsl:text>
                <span class="add__14">
                    <xsl:value-of select="$count_group" />
                </span>
            </td>

            <!--Продрлжительность урока-->
            <td>
                <span class="add__17">
                    <xsl:value-of select="property_value[property_id = 17]/value" />
                </span>
            </td>

            <!--Студия-->
            <td>
                <span class="user__areas">
                    <xsl:value-of select="/root/schedule_area[id = $areaId]/title" />
                </span>
            </td>

            <!--Действия-->
            <xsl:if test="//table-type = 'active'">
                <td width="140px">
                    <xsl:if test="//access_payment_create_client = 1">
                        <a class="action add_payment" onclick="makeClientPaymentPopup(0, {id}, saveClientPaymentCallback)" title="Добавить платеж"></a>
                    </xsl:if>

                    <xsl:if test="//access_user_edit_client = 1">
                        <a class="action edit" onclick="getClientPopup({id})" title="Редактировать данные"></a>
                    </xsl:if>

                    <xsl:if test="//access_user_archive_client = 1">
                        <a class="action archive user_archive" data-userid="{id}" title="Переместить в архив"></a>
                    </xsl:if>
                </td>
            </xsl:if>

            <xsl:if test="//table-type = 'archive'">
                <td>
                    <a class="action unarchive user_unarchive" href="#" data-userid="{id}" title="Восстановить из архива"></a>
                    <!--<a class="action delete user_delete" href="#" data-model_id="{id}" data-model_name="User" title="Безвозвратное удаление"></a>-->
                </td>
            </xsl:if>
        </tr>
    </xsl:template>

</xsl:stylesheet>