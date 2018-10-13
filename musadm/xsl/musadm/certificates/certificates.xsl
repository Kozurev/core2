<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="certificate">

            <div class="row buttons-panel">
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <a class="btn btn-pink certificate_create">Добавить сертификат</a>
                </div>
            </div>

            <style>
                .certificate .block {
                    margin-top: 0px !important;
                }
            </style>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped certificate center">
                    <thead>
                        <tr class="header">
                            <th>№</th>
                            <th>Дата продажи</th>
                            <th>Действителен до</th>
                            <th>Номер</th>
                            <th>Комментарии</th>
                            <th>Добавление <br/>комментария</th>
                        </tr>
                    </thead>

                    <tbody>
                        <xsl:apply-templates select="certificate" />
                    </tbody>
                </table>
            </div>

        </div>
    </xsl:template>


    <xsl:template match="certificate">
        <xsl:variable name="id" select="id" />
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="sell_date" /></td>
            <td><xsl:value-of select="active_to" /></td>
            <td><xsl:value-of select="number" /></td>
            <td>
                <div class="comments">
                    <xsl:apply-templates select="/root/certificate_note[certificate_id = $id]" />
                </div>
            </td>
            <td><a class="btn btn-pink add_comment" data-cert-id="{id}">+</a></td>
            <!--<td><a class="btn btn-pink certificate_delete" data-id="{id}">Удалить</a></td>-->
        </tr>
    </xsl:template>


    <xsl:template match="certificate_note">
        <div class="block">
            <div class="comment_header">
                <div class="author">
                    <xsl:value-of select="surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="name" />
                </div>
                <div class="date">
                    <xsl:value-of select="date" />
                </div>
            </div>

            <div class="comment_body">
                <xsl:value-of select="text" />
            </div>
        </div>
    </xsl:template>


</xsl:stylesheet>