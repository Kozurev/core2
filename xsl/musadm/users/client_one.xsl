<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="user">

        <xsl:variable name="balance">
            <xsl:value-of select="user_balance/amount" />
        </xsl:variable>

        <xsl:variable name="count_indiv">
            <xsl:value-of select="user_balance/individual_lessons_count" />
        </xsl:variable>

        <xsl:variable name="count_group">
            <xsl:value-of select="user_balance/group_lessons_count" />
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

        <xsl:variable name="kabinet">
            <xsl:choose>
                <xsl:when test="group_id = 5">
                    <xsl:value-of select="/root/wwwroot" />/balance?userid=<xsl:value-of select="id" />
                </xsl:when>
                <xsl:when test="group_id = 4">
                    <xsl:value-of select="/root/wwwroot" />/schedule?userid=<xsl:value-of select="id" />
                </xsl:when>
                <xsl:otherwise>#</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <tr class="{$class}" id="user_{id}">
            <!--Фамилия-->
            <td>
                <span class="user__fio">
                    <a>
                        <xsl:if test="$kabinet != '#'">
                            <xsl:attribute name="href"><xsl:value-of select="$kabinet" /></xsl:attribute>
                        </xsl:if>
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
                        <xsl:value-of select="comment" />
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
                <td>
                    <xsl:attribute name="width">
                        <xsl:choose>
                            <xsl:when test="/root/auth_user/email != '' and /root/my_calls_token != ''">120px</xsl:when>
                            <xsl:otherwise>140px</xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:if test="//access_payment_create_client = 1">
                        <a class="action add_payment" onclick="makeClientPaymentPopup(0, {id}, saveClientPaymentCallback)" title="Добавить платеж"></a>
                    </xsl:if>

                    <xsl:if test="//access_user_edit_client = 1">
                        <a class="action edit" onclick="getClientPopup({id})" title="Редактировать данные"></a>
                    </xsl:if>

                    <xsl:if test="/root/auth_user/email != '' and /root/my_calls_token != ''">
                        <a class="action phone" onclick="MyCalls.makeCall({/root/auth_user/id}, '{phone_number}', checkResponseStatus)" title="Совершить звонок"></a>
                    </xsl:if>

                    <xsl:if test="//access_user_archive_client = 1">
                        <a class="action archive user_activity" data-userid="{id}" title="Переместить в архив"></a>
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