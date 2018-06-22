<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <xsl:if test="structure_type = 'all'">
            <div class="finances_calendar">
                Период
                с: <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                по: <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                <button class="btn btn-success lids_show" >Показать</button>
            </div>
        </xsl:if>

        <table class="table lids">
            <form name="lid_form">
                <tr>
                    <td class="date" colspan="2"><input type="date" class="form-control date_inp"    name="control_date"/></td>
                    <td class="string"><input type="text" class="form-control" name="surname"  placeholder="Фамилия"/></td>
                    <td class="string"><input type="text" class="form-control" name="name"     placeholder="Имя"/></td>
                    <td class="string"><input type="text" class="form-control" name="number"   placeholder="Телефон"/></td>
                    <td class="string"><input type="text" class="form-control" name="vk"       placeholder="Ссылка вк"/></td>
                    <td class="string"><input type="text" class="form-control" name="source"   placeholder="Источник"/></td>
                    <td class="last"><button class="btn btn-success lid_submit">Добавить</button></td>
                </tr>
                <tr>
                    <td colspan="8">
                        <input type="text" class="form-control" name="comment"  placeholder="Комментарий"/>
                    </td>
                </tr>
            </form>

            <!-- <tr>
                <th>№</th>
                <th class="date">Дата</th>
                <th class="string">Фамилия</th>
                <th class="string">Имя</th>
                <th class="string">Телефон</th>
                <th class="string">VK</th>
                <th class="string">Источник</th>
                <th class="last">Статус</th>
            </tr> -->

        </table>

        <div class="row lids">
            <xsl:apply-templates select="lid" />
        </div>

    </xsl:template>


    <xsl:template match="lid">

        <xsl:variable name="status">
            <xsl:choose>
                <xsl:when test="property_value/id = '83'">agree</xsl:when>
                <xsl:when test="property_value/id = '81'">consult_wait</xsl:when>
                <xsl:when test="property_value/id = '82'">consult_was</xsl:when>
                <xsl:otherwise>not_choose</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <div class="card {$status}">
            <div>
                <xsl:value-of select="id" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </div>

            <div>
                <xsl:value-of select="number" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="vk" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="source" />
            </div>

            <div class="date">
                <input type="date" class="form-control date_inp lid_date" data-lidid="{id}" >
                    <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                </input>
            </div>

            <div class="comments">
                <input type="hidden" value="KOCTb|J|b" />
                <xsl:for-each select="lid_comment">
                    <xsl:variable name="author" select="author_id" />
                    <div class="block">
                        <div class="comment_header">
                            <div class="author">
                                <xsl:value-of select="//user[id = $author]/surname" />
                                <xsl:text> </xsl:text>
                                <xsl:value-of select="//user[id = $author]/name" />
                            </div>
                            <div class="date">
                                <xsl:value-of select="datetime" />
                            </div>
                        </div>

                        <div class="comment_body">
                            <xsl:value-of select="text" />
                        </div>
                    </div>
                </xsl:for-each>
            </div>

            <div class="comment_button_box">
                <button class="btn btn-default add_lid_comment" data-lidid="{id}">Добавить комментарий</button>
            </div>

            <div>
                <select name="status" class="form-control lid_status" data-lidid="{id}">
                    <xsl:variable name="status_id" select="property_value/id" />
                    <xsl:for-each select="/root/status">
                        <xsl:variable name="id" select="id" />
                        <option value="{$id}">
                            <xsl:if test="$id = $status_id">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="value" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
        </div>

    </xsl:template>

</xsl:stylesheet>