<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div class="lids">
            <div class="finances_calendar">
                Период
                с: <input type="date" class="form-control" name="date_from" value="{date_from}"/>
                по: <input type="date" class="form-control" name="date_to" value="{date_to}"/>
                <a class="btn btn-purple lids_show" >Показать</a>
            </div>

            <table class="table lids">
                <form name="lid_form">
                    <tr class="header">
                        <td class="date" colspan="2"><input type="date" class="form-control date_inp"    name="control_date"/></td>
                        <td class="string"><input type="text" class="form-control" name="surname"  placeholder="Фамилия"/></td>
                        <td class="string"><input type="text" class="form-control" name="name"     placeholder="Имя"/></td>
                        <td class="string"><input type="text" class="form-control" name="number"   placeholder="Телефон"/></td>
                        <td class="string"><input type="text" class="form-control" name="vk"       placeholder="Ссылка вк"/></td>
                        <td class="string"><input type="text" class="form-control" name="source"   placeholder="Источник"/></td>
                        <td class="last">
                            <a class="btn btn-purple lid_submit">Добавить</a>
                        </td>
                    </tr>
                    <tr class="header">
                        <td colspan="8">
                            <input type="text" class="form-control" name="comment"  placeholder="Комментарий"/>
                        </td>
                    </tr>
                </form>
            </table>

            <!-- <div class="row lids">
                <xsl:apply-templates select="lid" />
            </div> -->

            <section class="cards-section text-center">
                <!-- <div class="container"> -->
                    <div id="cards-wrapper" class="cards-wrapper row">
                        <xsl:choose>
                            <xsl:when test="count(lid) != 0">
                                <xsl:apply-templates select="lid" />
                            </xsl:when>
                            <xsl:otherwise>
                                <h2 class="section-title">Ничего не найдено</h2>
                            </xsl:otherwise>
                        </xsl:choose>
                    </div>
                <!-- </div> -->
            </section>
        </div>

    </xsl:template>


    <xsl:template match="lid">

        <xsl:variable name="status">
            <xsl:choose>
                <xsl:when test="property_value/id = '83'">green</xsl:when>
                <xsl:when test="property_value/id = '81'">blue</xsl:when>
                <xsl:when test="property_value/id = '82'">orange</xsl:when>
                <xsl:otherwise>not_choose</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <div class="item item-{$status} col-md-6 col-sm-12 col-xs-12">
            <div class="item-inner">
                <h3 class="title">
                    <xsl:value-of select="surname" /><xsl:text> </xsl:text>
                    <xsl:value-of select="name" /><xsl:text> </xsl:text>
                    <xsl:value-of select="patronimyc" /><xsl:text> </xsl:text>
                </h3>
                
                <xsl:if test="number != ''">
                    <p class="intro">
                        <span>Телефон: </span><xsl:value-of select="number" />
                    </p>
                </xsl:if>

                <xsl:if test="vk != ''">
                    <p class="intro">
                        <span>ВК: </span><xsl:value-of select="vk" />
                    </p>
                </xsl:if>

                <xsl:if test="source != ''">
                    <p class="intro">
                        <span>Источник: </span><xsl:value-of select="source" />
                    </p>
                </xsl:if>

                <p class="date">
                    <input type="date" class="form-control date_inp lid_date" data-lidid="{id}" >
                        <xsl:attribute name="value"><xsl:value-of select="control_date" /></xsl:attribute>
                    </input>
                </p>

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

                <div class="comment_button_box">
                    <a class="btn btn-purple add_lid_comment" data-lidid="{id}">Добавить комментарий</a>
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

                <!-- <a class="link" href="license.html"><span></span></a> -->
            </div><!--//item-inner-->
        </div><!--//item-->

    </xsl:template>

</xsl:stylesheet>