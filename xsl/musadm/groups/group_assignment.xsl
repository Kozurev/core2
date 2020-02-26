<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <style>
            .popup .row {
                margin-top: 5px;
            }
        </style>

        <div class="row popup-row-block" id="groupAssignments">
            <div class="col-lg-12">
                <h4><xsl:value-of select="group/title" /></h4>
            </div>
            <input type="hidden" id="groupId" value="{group/id}" />
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <xsl:if test="group/type = 1">
                    <div class="row">
                        <form id="groupSearch" method="get">
                            <div class="col-md-9">
                                <input type="text" id="groupUserQuery" class="form-control" placeholder="Фамилия" />
                            </div>

                            <div class="col-md-3">
                                <input class="btn btn-blue" type="submit" value="Поиск" />
                            </div>
                        </form>
                    </div>
                </xsl:if>

                <div class="row">
                    <xsl:choose>
                        <xsl:when test="group/type = 1">
                            <select class="form-control" id="groupUserList" multiple="multiple" size="7">
                                <xsl:apply-templates select="user" />
                            </select>
                        </xsl:when>
                        <xsl:otherwise>
                            <input type="number" class="form-control" id="groupUserList" />
                        </xsl:otherwise>
                    </xsl:choose>
                </div>

                <div class="row text-center">
                    <a href="#" class="btn btn-green" id="groupAppendClient">Добавить</a>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <select class="form-control" id="groupUserAssignments" multiple="multiple" size="10">
                    <xsl:choose>
                        <xsl:when test="group/type = 1">
                            <xsl:apply-templates select="group/user" />
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:apply-templates select="group/lid" />
                        </xsl:otherwise>
                    </xsl:choose>
                </select>

                <div class="row text-center">
                    <a href="#" class="btn btn-red" id="groupRemoveClient">Удалить</a>
                </div>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="user">
        <option value="{id}">
            <xsl:value-of select="surname" />
            <xsl:text> </xsl:text>
            <xsl:value-of select="name"/>
        </option>
    </xsl:template>

    <xsl:template match="lid">
        <option value="{id}">
            <xsl:value-of select="surname" />
            <xsl:text> </xsl:text>
            <xsl:value-of select="name"/>
            <xsl:text> </xsl:text>
            <xsl:value-of select="number"/>
        </option>
    </xsl:template>


</xsl:stylesheet>