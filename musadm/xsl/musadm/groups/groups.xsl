<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section>
            <div class="row buttons-panel">
                <div>
                    <a class="btn btn-blue" onclick="getGroupPopup(0)">Создать группу</a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped">
                    <thead>
                        <tr class="header">
                            <!--<th>id</th>-->
                            <th>Название</th>
                            <th>Учитель</th>
                            <th>Длит. занятия</th>
                            <th>Состав группы</th>
                            <th>Примечание</th>
                            <th>Действия</th>
                        </tr>
                    </thead>

                    <tbody>
                        <xsl:apply-templates select="schedule_group" />
                    </tbody>
                </table>
            </div>
        </section>
    </xsl:template>


    <xsl:template match="schedule_group">
        <xsl:variable name="teacher" select="teacher_id"/>
        <tr>
            <!--<td><xsl:value-of select="id" /></td>-->
            <td><xsl:value-of select="title" /></td>
            <td>
                <a href="{/root/wwwroot}/schedule/?userid={$teacher}">
                    <xsl:value-of select="user[id = $teacher]/surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="user[id = $teacher]/name" />
                </a>
            </td>
            <td><xsl:value-of select="duration" /></td>
            <td width="200px">
                <xsl:for-each select="user[id != $teacher]" >
                    <a href="{/root/wwwroot}/balance/?userid={id}">
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </a>
                    <br/>
                </xsl:for-each>
            </td>

            <td><xsl:value-of select="note" /></td>

            <td width="140px">
                <!--<a href="#" class="action edit group_edit" data-groupid="{id}"></a>-->
                <a class="action edit group_edit" onclick="getGroupPopup({id})"></a>
                <a class="action associate" onclick="getGroupComposition({id})"></a>
                <!--<a href="#" class="action archive group_archive" data-groupid="{id}"></a>-->
                <a class="action archive group_archive" onclick="updateActive('Schedule_Group', {id}, 0, refreshGroupTable);"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>