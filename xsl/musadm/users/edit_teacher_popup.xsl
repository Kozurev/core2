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
            </div>
            <hr/>
            <div class="column">
                <span>Имя</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/name}" name="name"  />
            </div>
            <hr/>
            <div class="column">
                <span>Отчество</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/patronymic}" name="patronymic"  />
            </div>
            <hr/>
            <div class="column">
                <span>Телефон</span>
            </div>
            <div class="column">
                <input class="form-control masked-phone" type="text" value="{user/phone_number}" name="phoneNumber" />
            </div>
            <hr/>
            <div class="column">
                <span>Логин</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{user/login}" name="login" autocomplete="off" />
            </div>
            <hr/>
            <div class="column">
                <span>Пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass1" autocomplete="off" />
            </div>
            <hr/>
            <div class="column">
                <span>Повторите пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass2" autocomplete="off" />
            </div>
            <hr/>
            <div class="column">
                <span>Инструмент</span>
            </div>
            <div class="column">
                <select name="property_20[]" class="form-control" multiple="multiple" size="4">
                    <option value="0">...</option>
                    <xsl:call-template name="property_list">
                        <xsl:with-param name="property_id" select="20" />
                    </xsl:call-template>
                </select>
            </div>
            <hr/>
            <div class="column">
                <span>Дата рождения</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{property_value[property_id = 28]/value}" name="property_28[]"  />
            </div>
            <hr/>

            <div class="column">
                <span>Филиалы</span>
            </div>
            <div class="column">
                <select class="form-control" name="areas[]" multiple="">
                    <xsl:for-each select="areas">
                        <xsl:variable name="areaId" select="id" />
                        <option value="{id}">
                            <xsl:if test="//assignments/area_id = $areaId">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <div class="column">
                <span>График для занятий</span>
            </div>
            <div class="column">
                <textarea name="comment" class="form-control">
                    <xsl:value-of select="user/comment" />
                </textarea>
            </div>

            <input type="hidden" name="id" value="{user/id}" />
            <input type="hidden" name="groupId" value="4" />
            <input type="hidden" name="active" value="1" />
            <input type="hidden" name="modelName" value="User" />


            <button class="popop_user_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>


    <xsl:template name="property_list">
        <xsl:param name="property_id" />

        <xsl:for-each select="property_list[property_id=$property_id]">
            <xsl:variable name="id" select="id" />
            <option value="{$id}">
                <xsl:if test="count(//property_value[property_id = $property_id][value_id = $id]) != 0">
                    <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <!--<xsl:value-of select="count(//property_value[property_id = $property_id][value_id = $id])" />-->
                <xsl:value-of select="value" />
            </option>
        </xsl:for-each>

    </xsl:template>


</xsl:stylesheet>