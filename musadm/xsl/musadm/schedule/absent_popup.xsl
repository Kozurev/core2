<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        dateFrom:    {required: true},
                        dateTo:      {required: true},
                    },
                    messages: {
                        dateFrom:   { required: "Это поле обязательноое к заполнению", },
                        dateTo:     { required: "Это поле обязательноое к заполнению", },
                    }
                });
            });
        </script>


        <xsl:variable name="date_from">
            <xsl:choose>
                <xsl:when test="absent/date_from != ''">
                    <xsl:value-of select="absent/date_from" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="//date_from" />
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="objectId">
            <xsl:choose>
                <xsl:when test="absent/object_id != 0">
                    <xsl:value-of select="absent/object_id" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="//object_id" />
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="typeId">
            <xsl:choose>
                <xsl:when test="absent/type_id != 0">
                    <xsl:value-of select="absent/type_id" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="//type_id" />
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>


        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Период "С" (включительно)</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="date" name="dateFrom" value="{$date_from}" />
            </div>
            <hr/>
            <div class="column">
                <span>Период "По (включительно)"</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="date" name="dateTo" value="{absent/date_to}" />
            </div>

            <xsl:if test="object/group_id = 4">
                <hr/>
                <div class="column">
                    <span>Время "С"</span><span style="color:red" >*</span>
                </div>
                <div class="column">
                    <input class="form-control" type="time" name="timeFrom" value="{absent/time_from}" />
                </div>

                <hr/>
                <div class="column">
                    <span>Время "По"</span><span style="color:red" >*</span>
                </div>
                <div class="column">
                    <input class="form-control" type="time" name="timeTo" value="{absent/time_to}" />
                </div>
            </xsl:if>
            <xsl:if test="absent/id = '' and type_id = 1 and object/group_id = 5">
                <div class="column">
                    <span>Напомнить администратору о выходе ученика</span>
                </div>
                <div class="column">
                    <input type="checkbox" id="absent_add_task" />
                </div>
            </xsl:if>

            <input type="hidden" name="id" value="{absent/id}" />
            <input type="hidden" value="{$objectId}" name="objectId" />
            <input type="hidden" value="{$typeId}" name="typeId" />
            <input type="hidden" value="Schedule_Absent" name="modelName" />

            <button class="popop_schedule_absent_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>