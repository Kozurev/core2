<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="teacher_one.xsl" />

    <xsl:template match="root">

        <!-- <xsl:if test="is_director = 1"> -->
            <h2>Список преподавателей</h2>

            <div class="row buttons-panel">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a class="btn btn-primary user_create" data-usergroup="4">Создать пользователя</a>
                </div>
            </div>
        <!-- </xsl:if> -->

        <div class="table-responsive">
            <table id="sortingTable" class="table table-striped">
                <thead>
                    <tr class="header">
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>Инструмент</th>
                        <th>График для занятий</th>
                        <th>Филиал</th>
                        <th class="center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <xsl:apply-templates select="user" />
                </tbody>
            </table>
        </div>

    </xsl:template>


</xsl:stylesheet>