<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        text:   { required: true },
                    },
                    messages: {
                        text:   { required: "Это поле обязательноое к заполнению" },
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Текст задачи</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="text" name="text" class="form-control" />
            </div>

            <div class="date">
                <div class="column">
                    <span>Дата контроля</span>
                </div>
                <div class="column">
                    <input type="date" name="date" class="form-control" value="{date}" />
                </div>
            </div>

            <xsl:variable name="associate" select="/root/associate" />
            <xsl:choose>
                <xsl:when test="$associate = 0">
                    <div class="column">
                        <span>Клиент</span>
                    </div>
                    <div class="column">
                        <select name="associate" class="form-control">
                            <option value="0"> ... </option>
                            <xsl:for-each select="user">
                                <option value="{id}">
                                    <xsl:value-of select="surname" />
                                    <xsl:text> </xsl:text>
                                    <xsl:value-of select="name" />
                                </option>
                            </xsl:for-each>
                        </select>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <input type="hidden" name="associate" value="{$associate}" />
                </xsl:otherwise>
            </xsl:choose>

            <div class="column">
                <span>Приоритет</span>
            </div>
            <div class="column">
                <select name="priority_id" class="form-control">
                    <xsl:for-each select="task_priority">
                        <option value="{id}">
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>

            <xsl:variable name="clientAreaId" select="/root/client_area_id" />
            <xsl:choose>
                <xsl:when test="$clientAreaId = 0">
                    <div class="column">
                        <span>Филиал</span>
                    </div>
                    <div class="column">
                        <select class="form-control" name="areaId">
                            <xsl:variable name="areaId" select="area_id" />
                            <option value="0"> ... </option>
                            <xsl:for-each select="schedule_area">
                                <xsl:if test="id = $areaId">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <option value="{id}"><xsl:value-of select="title" /></option>
                            </xsl:for-each>
                        </select>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <input type="hidden" name="areaId" value="{$clientAreaId}" />
                </xsl:otherwise>
            </xsl:choose>

            <!--<input type="hidden" name="user" value="{user/id}" />-->

            <button class="btn btn-default popup_task_submit" data-callback="{/root/callback}">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>