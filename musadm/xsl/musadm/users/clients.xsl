<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <xsl:if test="export_button_disable = 1">
            <style>
                .table {
                margin-bottom: 0px;
                }
                .positive {
                background-color: inherit !important;
                border-color: rgba(120, 252, 90, 0.7) !important;
                }
                .negative {
                background-color: inherit !important;
                border-color: rgba(247, 123, 107, 0.7) !important;
                }
                .neutral {
                background-color: inherit !important;
                border-color: rgba(240, 237, 234, 0.7) !important;
                }
                .contract {
                background: url("templates/template10/assets/images/contract.png");
                background-size: cover;
                display: inline-block;
                width:20px;
                height: 20px;
                margin-left: 5px;
                cursor: help;
                }
            </style>
        </xsl:if>


        <xsl:if test="table_type = 'active'">
            <xsl:if test="count(buttons_row) = 0 or buttons_row != 0">
                <div class="row buttons-panel">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <a href="#" class="btn btn-{page-theme-color} user_create" data-usergroup="5">Создать пользователя</a>
                    </div>
                    <xsl:if test="export_button_disable != 1">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                            <a href="client?action=export" class="btn btn-{page-theme-color}">Экспорт в Excel</a>
                        </div>
                    </xsl:if>
                </div>
            </xsl:if>
        </xsl:if>


        <div class="table-responsive">
            <table id="sortingTable" class="table table-striped table-statused">
                <thead>
                    <tr class="header">
                        <th>Фамилия имя</th>
                        <th>Телефон</th>
                        <th>Баланс</th>
                        <th>Кол-во занятий<br/> индив/групп</th>
                        <th>Длит.<br/>занятия</th>
                        <th>Студия</th>
                        <th>Действия</th>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="user" />
                </tbody>
            </table>
        </div>
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
            <td ><xsl:value-of select="property_value[property_id = 12]/value" /></td>

            <td width="150px">
                <xsl:value-of select="property_value[property_id = 13]/value" />
                <xsl:text> / </xsl:text>
                <xsl:value-of select="property_value[property_id = 14]/value" />
            </td>

            <!--Продрлжительность урока-->
            <td><xsl:value-of select="property_value[property_id = 17]/value" /></td>

            <!--Студия-->
            <td><xsl:value-of select="property_value[property_id = 15]/value" /></td>

            <!--Действия-->
            <xsl:if test="//table_type = 'active'">
                <td width="140px">
                    <a class="action add_payment user_add_payment" href="#" data-userid="{id}" title="Добавить платеж"></a>
                    <a class="action edit user_edit"        href="#" data-userid="{id}" data-usergroup="{group_id}" title="Редактировать данные"></a>
                    <a class="action archive user_archive"     href="#" data-userid="{id}" title="Переместить в архив"></a>
                </td>
            </xsl:if>

            <xsl:if test="//table_type = 'archive'">
                <td>
                    <a class="action unarchive user_unarchive"   href="#" data-userid="{id}" title="Восстановить из архива"></a>
                    <a class="action delete user_delete"      href="#" data-model_id="{id}" data-model_name="User" title="Безвозвратное удаление"></a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>

</xsl:stylesheet>