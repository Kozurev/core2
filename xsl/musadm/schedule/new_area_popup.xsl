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
                <span>Название</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="text" name="title" class="form-control" value="{schedule_area/title}" />
            </div><hr/>

            <div class="column">
                <span>Кол-во классов</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="number" name="countClasses" class="form-control" value="{schedule_area/count_classes}" />
            </div><hr/>

            <div class="column">
                <span>Сортировка</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input type="number" name="sorting" class="form-control" value="{schedule_area/sorting}" />
            </div><hr/>

            <input type="hidden" name="id" value="{schedule_area/id}" />
            <input type="hidden" name="active" value="1" />
            <input type="hidden" name="modelName" value="Schedule_Area" />

            <button class="popop_schedule_area_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>

</xsl:stylesheet>