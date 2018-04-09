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
                <xsl:choose>
                    <xsl:when test="count(property/id) != 0">
                        <th>id</th>
                        <th>Название свойства</th>
                        <xsl:apply-templates select="property" />
                    </xsl:when>
                    <xsl:otherwise>
                        <th>id</th>
                        <th>Значение</th>
                        <th>Редактировать</th>
                        <th>Удалить</th>
                        <xsl:apply-templates select="property_list_values" />
                    </xsl:otherwise>
                </xsl:choose>
            </table>

            <xsl:if test="count(property/id) = 0">
                <button class="btn btn-success" type="button">
                    <a href="/admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property_List_Values" class="link">
                        Добавть значение
                    </a>
                </button>
            </xsl:if>

            <button class="btn btn-success" type="button" style="visibility:hidden">
                <a href="/admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property_List_Values" class="link">а</a>
            </button>

            <div class="pagination">
                <a class="prev_page" href="/admin?menuTab=User&amp;action=show&amp;group_id={group_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="/admin?menuTab=User&amp;action=show&amp;group_id={group_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="property">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td class="table_structure">
                <a class="link" href="/admin?menuAction=show&amp;menuTab=List&amp;parent_id={id}">
                    <xsl:value-of select="title" />
                </a>
            </td>
            <td><xsl:value-of select="model_name" /></td>
        </tr>
    </xsl:template>


    <xsl:template match="property_list_values">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="value" /></td>
            <!--Редактирование-->
            <td><a href="/admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Property_List_Values&amp;model_id={id}" class="link updateLink" /></td>
            <!--Удаление-->
            <td><a href="/admin" data-model_name="Property_List_Values" data-model_id="{id}" class="delete deleteLink"></a></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>