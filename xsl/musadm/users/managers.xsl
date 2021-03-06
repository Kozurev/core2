<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:include href="manager_one.xsl" />

    <xsl:template match="root">

        <section class="section-bordered">
            <h4>Список менеджеров</h4>

            <xsl:if test="active-btn-panel = 1">
                <div class="row buttons-panel">
                    <xsl:if test="//access_user_create_manager = 1">
                        <div>
                            <a href="#" class="btn btn-primary user_create" data-usergroup="2">Добавить менеджера</a>
                        </div>
                    </xsl:if>

                    <xsl:if test="show-count-users = 1">
                        <div>
                            <span>Всего:</span><span><xsl:value-of select="count(user)" /></span>
                        </div>
                    </xsl:if>

                    <input type="hidden" />
                </div>
            </xsl:if>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped">
                    <thead>
                        <tr class="header">
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Студия</th>
                            <th class="center">Действия</th>
                        </tr>
                    </thead>

                    <tbody>
                        <xsl:apply-templates select="user" />
                    </tbody>
                </table>
            </div>
        </section>

    </xsl:template>


</xsl:stylesheet>