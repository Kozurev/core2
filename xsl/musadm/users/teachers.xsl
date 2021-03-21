<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="teacher_one.xsl" />

    <xsl:template match="root">

        <style>
            .teacher_areas {
                display: inline-block;
                max-height: 80px;
                overflow-y: auto;
            }
        </style>

        <section class="section-bordered">
            <h4>Список преподавателей</h4>

            <xsl:if test="active-btn-panel = 1">
                <div class="row buttons-panel">
                    <xsl:if test="access_user_create_teacher = 1">
                        <div>
                            <a class="btn btn-primary user_create" data-usergroup="4">Создать пользователя</a>
                        </div>
                    </xsl:if>

                    <xsl:if test="is_director = 1">
                        <div>
                            <a href="#" class="btn btn-primary edit_property_list" data-prop-id="20">Направление подготовки</a>
                        </div>
                    </xsl:if>

                    <xsl:if test="show-count-users = 1">
                        <div>
                            <span>Всего:</span>
                            <span><xsl:value-of select="/root/pagination/totalCount" /></span>
                        </div>
                    </xsl:if>
                </div>
            </xsl:if>


            <xsl:if test="access_user_read_teachers = 1">
                <div class="table-responsive">
                    <ul class="pagination pagination-sm">
                        <!--Первая страница-->
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changeClientsPage(1)">Первая</a>
                        </li>

                        <!--Указатель на предыдущую страницу-->
                        <li class="page-item">
                            <xsl:if test="pagination/currentPage = 1">
                                <xsl:attribute name="class">
                                    page-item disabled
                                </xsl:attribute>
                            </xsl:if>
                            <a class="page-link" href="#" onclick="changeClientsPage({pagination/prevPage})">
                                <span aria-hidden="true">←</span>
                            </a>
                        </li>
                        <!--Предыдущая страница-->
                        <xsl:if test="pagination/prevPage != 0">
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="changeClientsPage({pagination/prevPage})">
                                    <xsl:value-of select="pagination/prevPage" />
                                </a>
                            </li>
                        </xsl:if>

                        <!--Текущая страница-->
                        <li class="page-item active">
                            <a class="page-link" href="#" onclick="changeClientsPage({pagination/currentPage})">
                                <xsl:value-of select="pagination/currentPage" />
                            </a>
                        </li>

                        <!--Следующая страница-->
                        <xsl:if test="pagination/nextPage != 0">
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="changeClientsPage({pagination/nextPage})">
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
                            <a class="page-link" href="#" onclick="changeClientsPage({pagination/nextPage})">
                                <span aria-hidden="true">→</span>
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changeClientsPage({pagination/countPages})">Последняя</a>
                        </li>
                    </ul>
                    <table id="sortingTable" class="table table-striped">
                        <thead>
                            <tr class="header">
                                <th>ФИО</th>
                                <th>Телефон</th>
                                <th>Инструмент</th>
                                <th>Пометки</th>
                                <th>Стоп-лист</th>
                                <th width="185px">Филиал</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:apply-templates select="user" />
                        </tbody>
                    </table>
                </div>
            </xsl:if>
        </section>

    </xsl:template>


</xsl:stylesheet>