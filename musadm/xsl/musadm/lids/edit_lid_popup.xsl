<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <!--<script>-->
            <!--$(function(){-->
                <!--$("#createData").validate({-->
                    <!--rules: {-->
                        <!--surname:    {required: true, maxlength: 255},-->
                        <!--name:       {required: true, maxlength: 255},-->
                        <!--login:      {required: true, maxlength: 255},-->
                    <!--},-->
                    <!--messages: {-->
                        <!--surname: {-->
                            <!--required: "Это поле обязательноое к заполнению",-->
                            <!--maxlength: "Длина значения не должна превышать 255 символов"-->
                        <!--},-->
                        <!--name: {-->
                            <!--required: "Это поле обязательноое к заполнению",-->
                            <!--maxlength: "Длина значения не должна превышать 255 символов"-->
                        <!--},-->
                        <!--login: {-->
                            <!--required: "Это поле обязательноое к заполнению",-->
                            <!--maxlength: "Длина значения не должна превышать 255 символов"-->
                        <!--},-->
                    <!--}-->
                <!--});-->
            <!--});-->
        <!--</script>-->


        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Фамилия</span><!--<span style="color:red" >*</span>-->
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid/surname}" name="surname" />
            </div>
            <hr/>
            <div class="column">
                <span>Имя</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid/name}" name="name"  />
            </div>
            <hr/>
            <div class="column">
                <span>Номер телефона</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid/number}" name="number"  />
            </div>
            <hr/>
            <div class="column">
                <span>Ссылка ВК</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid/vk}" name="vk"  />
            </div>
            <hr/>
            <div class="column">
                <span>Дата контроля</span>
            </div>
            <div class="column">
                <input class="form-control" type="date" value="{today}" name="control_date"  />
            </div>
            <hr/>
            <div class="column">
                <span>Статус</span>
            </div>
            <div class="column">
                <select class="form-control" name="status_id" >
                    <xsl:for-each select="lid_status">
                        <option value="{id}">
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>
            <div class="column">
                <span>Филиал</span>
            </div>
            <div class="column">
                <select class="form-control" name="area_id" >
                    <option value="0"> ... </option>
                    <xsl:for-each select="schedule_area">
                        <option value="{id}">
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>
            <div class="column">
                <span>Источник</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid/source}" name="source"  />
            </div>
            <div class="column">
                <span>Комментарий</span>
            </div>
            <div class="column">
                <textarea class="form-control" name="comment" ></textarea>
            </div>


            <!--<input type="hidden" name="id" value="{lid/id}" />-->
            <!--<input type="hidden" name="modelName" value="Lid" />-->


            <button class="lid_submit btn btn-default">Сохранить</button>
        </form>


    </xsl:template>

</xsl:stylesheet>