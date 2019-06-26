<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row center">
            <div class="col-md-6 col-md-offset-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr class="header">
                            <th class="center">Название</th>
                            <th class="center">Пользователи</th>
                            <th class="center">Действия</th>
                        </tr>
                        <xsl:apply-templates select="core_access_group" />
                    </table>
                </div>
            </div>
        </div>
        <xsl:if test="//core_access_group/parent_id != 0 or count(//core_access_group) = 0">
            <div class="row buttons-panel center">
                <div><a class="btn btn-blue" onclick="Access.edit(0, {//parent_id}, editAccessGroupCallback)">Создать группу</a></div>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template match="core_access_group">
        <tr id="group_{id}">
            <td>
                <a class="title" href="{/root/wwwroot}/access?parent_id={id}">
                    <xsl:value-of select="title" />
                </a>
                <xsl:text> </xsl:text>
                (<xsl:value-of select="countChildren" />)
                <p><small class="description"><xsl:value-of select="description" /></small></p>
            </td>
            <td>
                <span id="countUsers_{id}"><xsl:value-of select="countUsers" /></span>
            </td>
            <td>
                <a class="action associate" onclick="Access.getUserList({id}, acessUserListCallBack)" title="Просмотреть/редактировать список полльзователей, принадлежащих группе"></a>
                <a class="action edit" onclick="Access.edit({id}, {//parent_id}, editAccessGroupCallback)" title="Редактировать данные группы"></a>
                <a class="action settings" href="{/root/wwwroot}/access?group_id={id}" title="Настройки прав доступа для группы"></a>
                <xsl:if test="parent_id != 0">
                    <a class="action delete" onclick="Access.remove({id}, accessGroupRemoveCallback)" title="Удалить группу"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>