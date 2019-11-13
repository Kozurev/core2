<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
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
                <select name="teacherId" class="form-control">
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
                <span>Длит. урока</span>
            </div>
            <div class="column">
                <input type="time" name="duration" class="form-control" value="{schedule_group/duration}" />
            </div>
            <hr/>

            <div class="column">
                <span>Примечание</span>
            </div>
            <div class="column">
                <textarea name="note"><xsl:value-of select="schedule_group/note" /></textarea>
            </div>

            <input type="hidden" name="id" value="{schedule_group/id}" />
            <input type="hidden" name="modelName" value="Schedule_Group" />
            <!--<input type="hidden" name="action" value="saveGroup" />-->

            <button class="btn btn-default" onclick="saveData('Main', refreshGroupTable)">Сохранить</button>
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