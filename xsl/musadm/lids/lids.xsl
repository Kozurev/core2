<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {

                    },
                    messages: {

                    }
                });
            });
        </script>

        <table class="table lids">
            <tr>
                <form name="lid_form">
                    <td class="date"><input type="date" class="form-control date_inp"    name="control_date"/></td>
                    <td class="string"><input type="text" class="form-control" name="surname"  placeholder="Фамилия"/></td>
                    <td class="string"><input type="text" class="form-control" name="name"     placeholder="Имя"/></td>
                    <td class="string"><input type="text" class="form-control" name="phone"    placeholder="Телефон"/></td>
                    <td class="string"><input type="text" class="form-control" name="vk"       placeholder="Ссылка вк"/></td>
                    <td class="comment"><input type="text" class="form-control" name="comment"  placeholder="Комментарий"/></td>
                    <td class="string"><input type="text" class="form-control" name="source"   placeholder="Источник"/></td>
                    <td class="last"><button class="btn btn-success lid_submit">Добавить</button></td>
                </form>
            </tr>

            <tr>
                <th class="date">Дата</th>
                <th class="string">Фамилия</th>
                <th class="string">Имя</th>
                <th class="string">Телефон</th>
                <th class="string">VK</th>
                <th class="comment">Комментарии</th>
                <th class="string">Источник</th>
                <th class="last">Статус</th>
                <!--<th>Действие</th>-->
            </tr>

            <xsl:apply-templates select="lid" />
        </table>
    </xsl:template>


    <xsl:template match="lid">
        <tr>
            <td class="date">
                <input type="date" class="form-control date_inp lid_date" data-lidid="{id}" >
                    <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                </input>
            </td>
            <td class="string"><xsl:value-of select="surname" /></td>
            <td class="string"><xsl:value-of select="name" /></td>
            <td class="string"><xsl:value-of select="phone" /></td>
            <td class="string"><xsl:value-of select="vk" /></td>
            <td class="comment">
                <xsl:for-each select="lid_comment">
                    <xsl:variable name="author" select="author_id" />
                    <div class="block">
                        <div class="author">
                            <xsl:value-of select="//user[id = $author]/surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="//user[id = $author]/name" />
                        </div>
                        <div class="comment_date">
                            <xsl:value-of select="datetime" />
                        </div>
                        <div class="comment">
                            <xsl:value-of select="text" />
                        </div>
                    </div>
                </xsl:for-each>
                <button class="btn btn-success add_lid_comment" data-lidid="{id}">Добавить комментарий</button>
            </td>
            <td class="string"><xsl:value-of select="source" /></td>
            <td class="last">
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
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>