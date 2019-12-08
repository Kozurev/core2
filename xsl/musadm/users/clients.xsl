<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="client_one.xsl" />

    <xsl:template match="root">


        <xsl:if test="active-btn-panel = 1">
            <div class="row buttons-panel">
                <xsl:if test="access_user_create_client = 1">
                    <div>
                        <a href="#" class="btn btn-{page-theme-color}" onclick="getClientPopup(0)">Создать пользователя</a>
                    </div>
                </xsl:if>

                <xsl:if test="active-export-btn = 1 and access_user_export = 1">
                    <div>
                        <button class="btn btn-{page-theme-color}" onclick="usersExport('client', $('#client-filter'))">
                            Экспорт в Excel
                        </button>
                    </div>
                </xsl:if>
                <xsl:if test="is_director = 1">
                <div><a class="btn btn-orange edit_property_list" data-prop-id="61">Причины отвала</a></div>
                </xsl:if>

                <xsl:if test="show-count-users = 1">
                    <div>
                        <span>Всего:</span>
                        <span id="total-clients-count">
                            <xsl:value-of select="/root/pagination/totalCount" />
                        </span>
                    </div>
                </xsl:if>

                <xsl:if test="avgAge != 0">
                    <div>
                        <span>Ср. возраст: <xsl:value-of select="avgAge" /></span>
                    </div>
                </xsl:if>

                <div>
                    <span>
                        Ср. стоимость занятия:
                        <xsl:value-of select="avgIndivCost" /> / <xsl:value-of select="avgGroupCost" />
                    </span>
                </div>
            </div>
        </xsl:if>

        <xsl:if test="active-btn-panel = 0 and active-export-btn = 1">
            <div class="row buttons-panel">
                <div>
                    <button class="btn btn-{page-theme-color}" onclick="usersExport('client', $('#client-filter'))">
                        Экспорт в Excel
                    </button>
                </div>

            </div>
        </xsl:if>

        <xsl:if test="access_user_read_clients = 1">

            <section>
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
                <ul class="pagination pagination-sm">
                    <li class="page-item">
                        <xsl:choose>
                            <xsl:when test="(paginate)">
                                <a class="page-link" href="?notPaginate">Показать всех</a>
                            </xsl:when>
                            <xsl:otherwise>
                                <a class="page-link" href="?paginate">Скрыть всех</a>
                            </xsl:otherwise>
                        </xsl:choose>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table id="sortingTable" class="table table-striped table-statused">
                        <thead>
                            <tr class="header">
                                <th>Фамилия имя</th>
                                <th>Телефон</th>
                                <th>Баланс</th>
                                <th>Кол-во занятий<br/> индив/групп</th>
                                <th>Длит.<br/>занятия</th>
                                <th>Студия</th>
                                <th>Действия</th>
                            </tr>
                        </thead>

                        <tbody>
                            <xsl:apply-templates select="user" />
                        </tbody>
                    </table>
                </div>
            </section>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>