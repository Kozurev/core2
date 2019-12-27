<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <xsl:choose>
            <xsl:when test="error != ''">
                <h3 style="color:red">Ошибка: <xsl:value-of select="error" /></h3>
            </xsl:when>
            <xsl:otherwise>
                <section class="senler-settings">
                    <h4>Настройки интеграции</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <form action="{/root/wwwroot}/integration/senler" id="senler-settings-group-form" method="GET">
                                <select class="form-control" name="group_id" onchange="$('#senler-settings-group-form').submit()">
                                    <option value="0">Укажите группу</option>
                                    <xsl:for-each select="groups">
                                        <option value="{id}">
                                            <xsl:if test="id = /root/current_group_id">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="title" />
                                        </option>
                                    </xsl:for-each>
                                </select>
                            </form>
                        </div>
                    </div>
                    <xsl:if test="current_group_id != 0">
                        <div class="row buttons-panel">
                            <div>
                                <a class="btn btn-green" onclick="showSenlerSettingPopup({current_group_id})">Добавить настройку</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="sortingTable" class="table table-striped">
                                <thead>
                                    <tr class="header">
                                        <th>Филиал</th>
                                        <th>Статус</th>
                                        <th>Группа подписки</th>
                                        <th>Инструмент</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <xsl:apply-templates select="setting" />
                                </tbody>
                            </table>
                        </div>
                    </xsl:if>
                </section>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="setting">
        <xsl:variable name="lidStatusId" select="lid_status_id" />
        <xsl:variable name="areaId" select="area_id" />
        <xsl:variable name="subscriptionId" select="senler_subscription_id" />
        <xsl:variable name="trainingDirectionId" select="training_direction_id" />
        <tr>
            <td>
                <xsl:value-of select="//area[id = $areaId]/title" />
            </td>
            <td>
                <xsl:choose>
                    <xsl:when test="$lidStatusId != 0">
                        <xsl:value-of select="//status[id = $lidStatusId]/title" />
                    </xsl:when>
                    <xsl:otherwise>
                        Архивация пользователя
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <xsl:value-of select="//subscription[subscription_id = $subscriptionId]/name" />
            </td>
            <td>
                <xsl:choose>
                    <xsl:when test="$trainingDirectionId = 0">
                        Все направления
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="//instrument[id = $trainingDirectionId]/value" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <a class="action edit" onclick="Senler.getSetting({id}, showSenlerSettingPopup)"><input type="hidden" /></a>
                <a class="action delete" onclick="Senler.deleteSetting({id}, deleteSenlerSettingCallback)"><input type="hidden" /></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>