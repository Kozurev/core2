<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        surname:    {required: true, maxlength: 255},
                        name:       {required: true, maxlength: 255},
                        login:      {required: true, maxlength: 255},
                    },
                    messages: {
                        surname: {
                            required: "Это поле обязательноое к заполнению",
                            maxlength: "Длина значения не должна превышать 255 символов"
                        },
                        name: {
                            required: "Это поле обязательноое к заполнению",
                            maxlength: "Длина значения не должна превышать 255 символов"
                        },
                        login: {
                            required: "Это поле обязательноое к заполнению",
                            maxlength: "Длина значения не должна превышать 255 символов"
                        },
                    }
                });
            });
        </script>

        <form name="createData" id="createData" action=".">

            <div class="column">
                <span>Фамилия</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/surname}" name="surname" />
            </div><hr/>

            <div class="column">
                <span>Имя</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/name}" name="name"  />
            </div><hr/>
            <div class="column">
                <span>Отчество</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/patronimyc}" name="patronimyc"  />
            </div><hr/>
            <div class="column">
                <span>Телефон</span>
            </div>
            <div class="column">
                <input class="form-control masked-phone" type="text" value="{user/phone_number}" name="phoneNumber" />
            </div><hr/>

            <div class="column">
                <span>Логин</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/login}" name="login" />
            </div><hr/>

            <div class="column">
                <span>Пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass1" />
            </div><hr/>

            <div class="column">
                <span>Повторите пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass2" />
            </div><hr/>


            <input type="hidden" name="id" value="{user/id}" />
            <input type="hidden" name="groupId" value="2" />
            <input type="hidden" name="active" value="1" />
            <input type="hidden" name="modelName" value="User" />


            <button class="popop_user_submit btn btn-default">Сохранить</button>
        </form>

        <script>
            $(".masked-phone").mask("+79999999999");
        </script>
    </xsl:template>


</xsl:stylesheet>