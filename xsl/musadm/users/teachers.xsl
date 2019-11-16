<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="teacher_one.xsl" />

    <xsl:template match="root">
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
                            <span>Всего:</span><span><xsl:value-of select="count(user)" /></span>
                        </div>
                    </xsl:if>
                </div>
            </xsl:if>


            <xsl:if test="access_user_read_teachers = 1">
                <div class="table-responsive">
                    <table id="sortingTable" class="table table-striped">
                        <thead>
                            <tr class="header">
                                <th>ФИО</th>
                                <th>Телефон</th>
                                <th>Инструмент</th>
                                <th>График для занятий</th>
                                <th>Стоп-лист</th>
                                <th>Филиал</th>
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