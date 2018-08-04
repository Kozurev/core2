<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Название</th>
                    <th>Модель</th>
                    <th>Активность</th>
                    <th>Действия</th>
                    <!--<th>Редактировать</th>-->
                    <!--<th>Удалить</th>-->
                </tr>
                <xsl:apply-templates select="admin_menu" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Admin_Menu&amp;parent_id={parent_id}" class="link">
                    Создать вкладку
                </a>
            </button>
        </div>
    </xsl:template>


    <xsl:template match="admin_menu">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <a class="link" href="admin?menuTab=Menu&amp;menuAction=show&amp;parent_id={id}">
                    <xsl:value-of select="title" />
                </a>
            </td>
            <td><xsl:value-of select="model" /></td>

            <!--Активность-->
            <td>
                <input type="checkbox" class="activeCheckbox">
                    <xsl:attribute name="model_name">Admin_Menu</xsl:attribute>
                    <xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
                    <xsl:if test="active = 1">
                        <xsl:attribute name="checked">true</xsl:attribute>
                    </xsl:if>
                </input>
            </td>

            <td>
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Admin_Menu&amp;parent_id={parent_id}&amp;model_id={id}" class="link updateLink" />
                <a href="admin" data-model_name="Admin_Menu" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>



</xsl:stylesheet>