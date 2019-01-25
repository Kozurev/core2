<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="../schedule/assignments/areas_assignments.xsl" />

    <xsl:template match="root">

        <xsl:if test="is_director = 1">
            <h2>Список преподавателей</h2>

            <div class="row buttons-panel">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a class="btn btn-primary user_create" data-usergroup="4">Создать пользователя</a>
                </div>
            </div>
        </xsl:if>

        <div class="table-responsive">
            <table id="sortingTable" class="table table-striped">
                <thead>
                    <tr class="header">
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Инструмент</th>
                        <th>График для занятий</th>
                        <th>Филиал</th>
                        <th class="center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:apply-templates select="user" />
                </tbody>
            </table>
        </div>

    </xsl:template>


    <xsl:template match="user">
        <tr>
            <td>
                <a href="{/root/wwwroot}/schedule/?userid={id}">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="patronimyc" />
                </a>
            </td>
            <td><xsl:value-of select="phone_number" /></td>
            <td><xsl:value-of select="property_value[property_id = 20]/value" /></td>
            <td><xsl:value-of select="property_value[property_id = 31]/value" /></td>

            <td>
                <span data-areas="User_{id}"><xsl:apply-templates select="areas" /></span>
            </td>

            <td width="140px">
                <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="User"></a>
                <a class="action archive user_archive"     href="#" data-userid="{id}"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>