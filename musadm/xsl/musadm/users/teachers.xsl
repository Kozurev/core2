<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

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
                        <!--<th>Фамилия</th>-->
                        <!--<th>Имя</th>-->
                        <!--<th>Отчество</th>-->
                        <th>ФИО</th>
                        <th>Инструмент</th>
                        <th>График для занятий</th>
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
            <!--<td><xsl:value-of select="name" /></td>-->
            <!--<td><xsl:value-of select="patronimyc" /></td>-->
            <td><xsl:value-of select="property_value[property_id = 20]/value" /></td>
            <td><xsl:value-of select="property_value[property_id = 31]/value" /></td>
            <td>
                <xsl:if test="/root/is_director = 1">
                    <a class="action edit user_edit" href="#" data-userid="{id}" data-usergroup="{group_id}"></a>
                    <a class="action archive user_archive"     href="#" data-userid="{id}"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>