<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">Филлиалы</h3>
            <table class="table">
                <th>id</th>
                <th>Название</th>
                <th>Кол-во классов</th>
                <th>Действия</th>
                <xsl:apply-templates select="schedule_area" />
            </table>

            <button class="btn button" type="button">
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Area" class="link">
                    Новый филиал
                </a>
            </button>
        </div>
    </xsl:template>


    <xsl:template match="schedule_area">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>
            <td><xsl:value-of select="count_classess" /></td>
            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Area&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Area" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>