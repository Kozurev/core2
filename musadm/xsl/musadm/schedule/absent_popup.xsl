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


        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Период "С" (включительно)</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="date" name="dateFrom" />
            </div>
            <hr/>
            <div class="column">
                <span>Период "До (включительно)"</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="date" name="dateTo"  />
            </div>
            <div class="column">
                <span>Напомнить администратору о выходе ученика</span>
            </div>
            <div class="column">
                <input type="checkbox" id="absent_add_task" />
            </div>

            <input type="hidden" name="id" value="" />
            <input type="hidden" value="{//clientid}" name="clientId" />
            <input type="hidden" value="{//typeid}" name="typeId" />
            <input type="hidden" value="Schedule_Absent" name="modelName" />

            <button class="popop_schedule_absent_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>