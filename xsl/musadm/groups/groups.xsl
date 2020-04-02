<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section>
            <div class="row buttons-panel">
                <input type="hidden" />
                <xsl:if test="access_group_create = 1">
                    <div>
                        <a class="btn btn-blue" onclick="getGroupPopup(0)">Создать группу</a>
                    </div>
                </xsl:if>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <ul class="pagination pagination-sm">
                        <!--Первая страница-->
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable(1)">Первая</a>
                        </li>

                        <!--Указатель на предыдущую страницу-->
                        <li class="page-item">
                            <xsl:if test="pagination/currentPage = 1">
                                <xsl:attribute name="class">
                                    page-item disabled
                                </xsl:attribute>
                            </xsl:if>
                            <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/prevPage})">
                                <span aria-hidden="true">←</span>
                            </a>
                        </li>
                        <!--Предыдущая страница-->
                        <xsl:if test="pagination/prevPage != 0">
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/prevPage})">
                                    <xsl:value-of select="pagination/prevPage" />
                                </a>
                            </li>
                        </xsl:if>

                        <!--Текущая страница-->
                        <li class="page-item active">
                            <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/currentPage})">
                                <xsl:value-of select="pagination/currentPage" />
                            </a>
                        </li>

                        <!--Следующая страница-->
                        <xsl:if test="pagination/nextPage != 0">
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/nextPage})">
                                    <xsl:value-of select="pagination/nextPage" />
                                </a>
                            </li>
                        </xsl:if>

                        <!--Указатель на следующую страницу-->
                        <li class="page-item">
                            <xsl:if test="pagination/currentPage = pagination/countPages">
                                <xsl:attribute name="class">
                                    page-item disabled
                                </xsl:attribute>
                            </xsl:if>
                            <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/nextPage})">
                                <span aria-hidden="true">→</span>
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#" onclick="event.preventDefault(); refreshGroupTable({pagination/countPages})">Последняя</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped">
                    <thead>
                        <tr class="header">
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
            <td><xsl:value-of select="title" /></td>
            <td>
                <a href="{/root/wwwroot}/lk?userid={$teacher}">
                    <xsl:value-of select="user[id = $teacher]/surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="user[id = $teacher]/name" />
                </a>
            </td>
            <td><xsl:value-of select="duration" /></td>
            <td width="200px">
                <xsl:choose>
                    <xsl:when test="type = 1">
                        <xsl:for-each select="user[id != $teacher]" >
                            <a href="{/root/wwwroot}/balance/?userid={id}">
                                <xsl:value-of select="surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="name" />
                            </a>
                            <br/>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:for-each select="lid" >
                            <a href="#" class="info-by-id" data-model="Lid" data-id="{id}">
                                <xsl:value-of select="surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="name" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="number" />
                            </a>
                            <br/>
                        </xsl:for-each>
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <td><xsl:value-of select="note" /></td>

            <td width="140px">
                <xsl:if test="/root/access_group_edit = 1">
                    <a class="action edit group_edit" onclick="getGroupPopup({id})"></a>
                    <a class="action associate" onclick="getGroupComposition({id})"></a>
                </xsl:if>

                <xsl:if test="/root/access_group_delete = 1">
                    <a class="action archive group_archive" onclick="updateActive('Schedule_Group', {id}, 0, refreshGroupTable);"></a>
                </xsl:if>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>