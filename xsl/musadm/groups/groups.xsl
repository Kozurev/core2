<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div style="text-align: right">
            <button class="btn btn-success group_create">Создать группу</button>
        </div>

        <table id="sortingTable" class="table ">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Название</th>
                    <th>Учитель</th>
                    <th>Длительность</th>
                    <th>Состав группы</th>
                    <th>Действия</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="schedule_group" />
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="schedule_group">
        <xsl:variable name="teacher" select="teacher_id"/>
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>
            <td>
                <a href="./?userid={id}">
                    <xsl:value-of select="user[id = $teacher]/surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="user[id = $teacher]/name" />
                </a>
            </td>
            <td><xsl:value-of select="duration" /></td>
            <td>
                <xsl:for-each select="user[id != $teacher]" >
                    <a href="./?userid={id}">
                        <xsl:value-of select="surname" />
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="name" />
                    </a>
                    <br/>
                </xsl:for-each>
            </td>

            <td>
                <a href="#" class="action edit group_edit" data-groupid="{id}"></a>
                <a href="#" class="action delete group_delete" data-groupid="{id}"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>