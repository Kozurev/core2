<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="client_one.xsl" />

    <xsl:template match="root">

        <!-- <xsl:if test="buttons-panel = 1"> -->
            <style>
                .table {
                margin-bottom: 0px;
                }
                .positive {
                background-color: inherit !important;
                border-color: rgba(120, 252, 90, 0.7) !important;
                }
                .negative {
                background-color: inherit !important;
                border-color: rgba(247, 123, 107, 0.7) !important;
                }
                .neutral {
                background-color: inherit !important;
                border-color: rgba(240, 237, 234, 0.7) !important;
                }
                .contract {
                background: url("templates/template10/assets/images/contract.png");
                background-size: cover;
                display: inline-block;
                width:20px;
                height: 20px;
                margin-left: 5px;
                cursor: help;
                }
            </style>
        <!-- </xsl:if> -->


        <xsl:if test="active-btn-panel = 1">
            <div class="row buttons-panel">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a href="#" class="btn btn-{page-theme-color} user_create" data-usergroup="5">Создать пользователя</a>
                </div>

                <xsl:if test="active-export-btn = 1">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <a href="client?action=export" class="btn btn-{page-theme-color}">Экспорт в Excel</a>
                    </div>
                </xsl:if>

                <xsl:if test="show-count-users = 1">
                    <div class="col-lg-1 col-md-2 col-sm-3 col-xs-4">
                        <span>Всего:</span><span id="total-clients-count"><xsl:value-of select="count(user)" /></span>
                    </div>
                </xsl:if>
            </div>
        </xsl:if>


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
    </xsl:template>


    

</xsl:stylesheet>