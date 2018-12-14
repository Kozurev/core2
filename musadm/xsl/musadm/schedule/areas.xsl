<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <h2>Филиалы</h2>

        <div class="row buttons-panel">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <a href="#" class="btn btn-green schedule_area_edit" data-area_id="0">Создать филиал</a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="sortingTable" class="table table-striped">
                <thead>
                    <tr class="header">
                        <th>Название</th>
                        <th>Кол-во классов</th>
                        <th>Активность</th>
                        <th>Действия</th>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="schedule_area" />
                </tbody>
            </table>
        </div>
    </xsl:template>


    <xsl:template match="schedule_area">

        <tr>
            <td><a href="{/root/wwwroot}/schedule/{path}"><xsl:value-of select="title" /></a></td>
            <td><xsl:value-of select="count_classess" /></td>
            <td>
                <!--<input type="checkbox" name="schedule_area_active" data-area_id="{id}">-->
                    <!--<xsl:if test="active = 1">-->
                        <!--<xsl:attribute name="checked">true</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->

                <input type="checkbox" name="schedule_area_active" id="schedule_area_active{position()}" data-area_id="{id}" class="checkbox-new" >
                    <xsl:if test="active = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="schedule_area_active{position()}" class="label-new">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
            </td>

            <td>
                <a class="action edit schedule_area_edit" href="#" data-area_id="{id}"></a>
                <a class="action delete schedule_area_delete" href="#" data-area_id="{id}"></a>
            </td>

        </tr>
    </xsl:template>

</xsl:stylesheet>