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
                <form name="lid_form" id="createData">
                    <td><input type="date" class="form-control" name="control_date"/></td>
                    <td><input type="text" class="form-control" name="surname"  placeholder="Фамилия"/></td>
                    <td><input type="text" class="form-control" name="name"     placeholder="Имя"/></td>
                    <td><input type="text" class="form-control" name="phone"    placeholder="Телефон"/></td>
                    <td><input type="text" class="form-control" name="vk"       placeholder="Ссылка вк"/></td>
                    <td><input type="text" class="form-control" name="comment"  placeholder="Комментарий"/></td>
                    <td><input type="text" class="form-control" name="source"   placeholder="Источник"/></td>
                    <td>
                        <!--<select name="status" class="form-control">-->
                            <!--<xsl:for-each select="status">-->
                                <!--<option value="{id}">-->
                                    <!--<xsl:value-of select="value" />-->
                                <!--</option>-->
                            <!--</xsl:for-each>-->
                        <!--</select>-->
                    </td>
                    <td><button class="btn btn-success lid_submit">Добавить</button></td>
                </form>
            </tr>

            <tr>
                <th>Дата</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>VK</th>
                <th>Комментарии</th>
                <th>Источник</th>
                <th>Статус</th>
                <th>Действие</th>
            </tr>

            <xsl:apply-templates select="lid" />
        </table>
    </xsl:template>


    <xsl:template match="lid">
        <tr>
            <td>
                <input type="date" class="form-control" data-lidid="{id}" >
                    <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                </input>
            </td>
            <td><xsl:value-of select="surname" /></td>
            <td><xsl:value-of select="name" /></td>
            <td><xsl:value-of select="phone" /></td>
            <td><xsl:value-of select="vk" /></td>
            <td>
                <xsl:for-each select="lid_comment">
                    <xsl:variable name="author" select="author_id" />
                    <div class="block">
                        <div class="author">
                            <xsl:value-of select="//user[id = $author]/surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="//user[id = $author]/name" />
                        </div>
                        <div class="date">
                            <xsl:value-of select="datetime" />
                        </div>
                        <div class="comment">
                            <xsl:value-of select="text" />
                        </div>
                    </div>

                </xsl:for-each>
            </td>
            <td><xsl:value-of select="source" /></td>
            <td></td>
            <td></td>
        </tr>
    </xsl:template>

</xsl:stylesheet>