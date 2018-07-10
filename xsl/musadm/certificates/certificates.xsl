<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="certificate">
            <div style="text-align: right; margin-bottom:20px">
                <a class="btn btn-pink certificate_create">Добавить сертификат</a>
            </div>

            <style>
                .certificate .block {
                    margin-top: 0px !important;
                }
            </style>

            <table id="sortingTable" class="table table-striped certificate">
                <thead>
                    <tr class="header">
                        <th>№</th>
                        <th>Дата продажи</th>
                        <th>Действителен до</th>
                        <th>Номер</th>
                        <th>Комментарии</th>
                        <th>Добавление комментария</th>
                    </tr>
                </thead>

                <tbody>
                    <xsl:apply-templates select="certificate" />
                </tbody>
            </table>
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
            <td><a class="btn btn-pink add_comment" data-cert-id="{id}">Добавить комментарий</a></td>
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