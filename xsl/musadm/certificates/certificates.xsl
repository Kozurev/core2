<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="certificate">
            <xsl:if test="access_create = 1">
                <div class="row buttons-panel">
                    <a class="btn btn-pink" onclick="editCertificatePopup(0)">Добавить сертификат</a>
                </div>
            </xsl:if>

            <style>
                .certificate .block {
                    margin-top: 0px !important;
                }
            </style>

            <div class="table-responsive">
                <table id="sortingTable" class="table table-striped certificate center">
                    <thead>
                        <tr class="header">
                            <th class="center">№</th>
                            <th class="center">Дата продажи</th>
                            <th class="center">Действителен до</th>
                            <th class="center">Номер</th>
                            <th class="center">Филиал</th>
                            <th class="center">Комментарии</th>
                            <th class="center">Действия</th>
                        </tr>
                    </thead>

                    <tbody>
                        <xsl:apply-templates select="certificate" />
                    </tbody>
                </table>
            </div>
        </section>
    </xsl:template>


    <xsl:template match="certificate">
        <xsl:variable name="id" select="id" />
        <xsl:variable name="areaId" select="area_id" />
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="sell_date" /></td>
            <td><xsl:value-of select="active_to" /></td>
            <td><xsl:value-of select="number" /></td>
            <td><xsl:value-of select="//schedule_area[id = $areaId]/title" /></td>
            <td>
                <div class="comments">
                    <xsl:apply-templates select="/root/certificate_note[certificate_id = $id]" />
                </div>
            </td>
            <td>
                <div class="row">
                    <xsl:if test="//access_comment = 1">
                        <a class="action comment" onclick="addNewCertificateNotePopup({id})"></a>
                    </xsl:if>

                    <xsl:if test="//access_edit = 1">
                        <a class="action edit" onclick="editCertificatePopup({id})"></a>
                    </xsl:if>

                    <xsl:if test="//access_delete = 1">
                        <a class="action delete" onclick="deleteItem('Certificate', {id}, refreshCertificatesTable)"></a>
                    </xsl:if>
                </div>
            </td>
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