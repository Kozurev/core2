<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <script>
            $(function(){
            $("#createData").validate({
            rules: {
            timeFrom:    {required: true},
            timeTo:      {required: true},
            },
            messages: {
            timeFrom:   { required: "Это поле обязательноое к заполнению", },
            timeTo:     { required: "Это поле обязательноое к заполнению", },
            }
            });
            });
        </script>


        <form name="createData" id="createData" action=".">
            <center><h2>Изменение времени</h2></center>

            <div class="column">
                <span>Время начала</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" name="timeFrom" type="time" />
            </div>
            <hr/>

            <div class="column">
                <span>Время окончания</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" name="timeTo" type="time" />
            </div>
            <hr/>

            <input type="hidden" value="{lesson_id}" name="lesson_id" />
            <input type="hidden" value="{date}" name="date" />

            <button class="popop_schedule_time_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>