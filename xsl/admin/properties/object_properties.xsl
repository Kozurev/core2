<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <table class="table">
                <th>id</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Тип</th>
                <!--<th>Активность</th>-->
                <th>Принадлежность разделу</th>
                <!--<th>Редактировать</th>-->
                <!--<th>Удалить</th>-->
                <xsl:apply-templates select="property_dir" />
                <xsl:apply-templates select="property" />
            </table>

            <button class="btn button" type="button" style="visibility:hidden">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property_List_Values" class="link">а</a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Properties&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Properties&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>

        </div>
    </xsl:template>


    <xsl:template match="property_dir">
        <tr>
            <td><xsl:value-of select="id"/></td>

            <td class="table_structure">
                <a class="link dir" href="admin?menuTab=Properties&amp;menuAction=show&amp;parent_id={id}&amp;model_id={//obj_id}&amp;model_name={//model_name}">
                    <xsl:value-of select="title" />
                </a>
            </td>

            <td><xsl:value-of select="description"/></td>
            <td></td>
            <!--Активность-->

            <td></td>

            <!--&lt;!&ndash;Редактирование&ndash;&gt;-->
            <!--<td><a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property_Dir&amp;model_id={id}" class="link updateLink" /></td>-->
            <!--&lt;!&ndash;Удаление&ndash;&gt;-->
            <!--<td><a href="admin" data-model_name="Property_Dir" data-model_id="{id}" class="delete deleteLink"></a></td>-->
        </tr>
    </xsl:template>

    <xsl:template match="property">
        <tr>
            <td><xsl:value-of select="id"/></td>
            <td class="table_structure"><xsl:value-of select="title" /></td>
            <td><xsl:value-of select="description"/></td>
            <td><xsl:value-of select="type"/></td>

            <!--Активность-->
            <!--<td>-->
                <!--<input type="checkbox" class="activeCheckbox">-->
                    <!--<xsl:attribute name="model_name">Property</xsl:attribute>-->
                    <!--<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>-->
                    <!--<xsl:if test="active = 1">-->
                        <!--<xsl:attribute name="checked">true</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->
            <!--</td>-->

            <xsl:variable name="obj_id" select="//obj_id" />
            <!--<xsl:variable name="checked" >-->
                <!--<xsl:if test="belongs = '1'">checked</xsl:if>-->
            <!--</xsl:variable>-->
            <td>
                <input class="checkbox active_property_for_object" type="checkbox" data-obj_name="{//model_name}" data-obj_id="{$obj_id}" data-prop_id="{id}">
                    <xsl:if test="belongs = 1">
                        <xsl:attribute name="checked">true</xsl:attribute>
                    </xsl:if>
                </input>
            </td>

            <!--&lt;!&ndash;Редактирование&ndash;&gt;-->
            <!--<td><a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property&amp;model_id={id}" class="link updateLink" /></td>-->

            <!--&lt;!&ndash;Удаление&ndash;&gt;-->
            <!--<td><a href="admin" data-model_name="Property" data-model_id="{id}" class="delete deleteLink"></a></td>-->
        </tr>
    </xsl:template>




</xsl:stylesheet>