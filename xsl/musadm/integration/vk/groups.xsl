<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section>
            <h4>Группы ВК</h4>
            <div class="row buttons-panel">
                <div>
                    <a class="btn btn-green" onclick="showVkGroupPopup()">Создать группу</a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped">
                    <thead>
                        <tr class="header">
                            <th>#</th>
                            <th>Название</th>
                            <th>Ссылка</th>
                            <th>вк id</th>
                            <th>Ключ доступа</th>
                            <th>Секретный ключ Callback API</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="vk_group" />
                    </tbody>
                </table>
            </div>

<!--            <div class="callout-block callout-info" style="margin-top: 100px">-->
<!--                <div class="icon-holder">-->
<!--                    <i class="fa fa-info-circle"><input type="hidden" /></i>-->
<!--                </div>&lt;!&ndash;//icon-holder&ndash;&gt;-->
<!--                <div class="content">-->
<!--                    <h4 class="callout-title">Внимание</h4>-->
<!--                    <p>При создании первой из групп указание секретного ключа является обязательным.</p>-->
<!--                    <p>Для работы с API вконтакте необходим секретный ключ хотя бы одной из выших групп, сгенерированный в настройках сообщества.</p>-->
<!--                </div>&lt;!&ndash;//content&ndash;&gt;-->
<!--            </div>-->
        </section>
    </xsl:template>


    <xsl:template match="vk_group">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>
            <td><a href="{link}" target="_blank"><xsl:value-of select="link" /></a></td>
            <td><xsl:value-of select="vk_id" /></td>
            <td><xsl:value-of select="secret_key" /></td>
            <td><xsl:value-of select="secret_callback_key" /></td>
            <td>
                <a class="action edit" onclick="Vk.getGroup({id}, showVkGroupPopup)"><input type="hidden" /></a>
                <a class="action delete" onclick="Vk.remove({id}, removeVkGroupCallback)"><input type="hidden" /></a>
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>