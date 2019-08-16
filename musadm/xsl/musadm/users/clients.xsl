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

                <xsl:if test="show-count-users = 1">
                    <div>
                        <span>Всего:</span><span id="total-clients-count"><xsl:value-of select="count(user)" /></span>
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


        <xsl:if test="access_user_read_clients = 1">
            <section>
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