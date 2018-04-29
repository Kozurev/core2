<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <!--<xsl:variable name="modelid" select="object_id" />-->
        <!--<xsl:variable name="modelname" select="model_name" />-->

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        title:    {required: true, maxlength: 255},
                    },
                    messages: {
                        surname: {
                            required: "Это поле обязательноое к заполнению",
                            maxlength: "Длинна значения не должна превышать 255 символов"
                        }
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Название</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{schedule_group/title}" name="title" />
            </div>
            <hr/>
            <div class="column">
                <span>Учитель</span>
            </div>
            <div class="column">
                <select name="teacher_id" class="form-control">
                    <xsl:for-each select="user[group_id = 4]">
                        <option value="{id}">
                            <xsl:if test="id = //schedule_group/teacher_id">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <div class="column">
                <span>Длительность урока</span>
            </div>
            <div class="column">
                <input type="time" name="duration" class="form-control" value="{schedule_group/duration}" />
            </div>
            <hr/>

            <div class="column">
                <span>Состав группы</span>
            </div>
            <div class="column">
                <select name="clients[]" multiple="multiple" class="form-control" size="8">
                    <xsl:for-each select="user[group_id = 5]">
                        <xsl:variable name="id" select="id"/>
                        <option value="{id}">
                            <xsl:if test="count(//schedule_group/user[id = $id]) != 0">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="surname" />
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <input type="hidden" name="id" value="{schedule_group/id}" />
            <input type="hidden" name="action" value="saveGroup" />

            <button class="popop_group_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>


    <xsl:template name="property_list">
        <xsl:param name="property_id" />

        <xsl:for-each select="property_list[property_id=$property_id]">
            <xsl:variable name="id" select="id" />
            <option value="{$id}">
                <xsl:if test="count(//property_value[id=$id]/value) != 0">
                    <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="value" />
            </option>
        </xsl:for-each>

    </xsl:template>


</xsl:stylesheet>