<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <script>
            $(function(){
                $("#createData").validate({
                    rules: {
                        surname:    {required: true, maxlength: 255},
                        name:       {required: true, maxlength: 255},
                        //phoneNumber:{required: true, maxlength: 255},
                        login:      {required: true, maxlength: 255},
                        <xsl:if test="count(user/id) = 0">
                            password: {required: true, maxlength: 255}
                        </xsl:if>
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
                        <xsl:if test="count(user/id) = 0">
                        password: {
                            required: "Это поле обязательно к заполнению",
                            maxlength: "Длина значения не должна превышать 255 символов"
                        }
                        </xsl:if>
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
                <input class="form-control" type="text" value="{user/login}" name="login" />
            </div>
                <hr/>
            <div class="column">
                <span>Пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass1" />
            </div>
                <hr/>
            <div class="column">
                <span>Повторите пароль</span>
            </div>
            <div class="column">
                <input class="form-control" type="password" value="" name="pass2" />
            </div>
            <hr/>
            <div class="column">
                <span>Дополнительный телефон</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{property_value[property_id=16]/value}" name="property_16[]" />
            </div>
            <hr/>
            <div class="column">
                <span>Год рождения</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{property_value[property_id=28]/value}" name="property_28[]" />
            </div>
                <hr/>
            <div class="column">
                <span>Ссылка вконтакте</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{property_value[property_id=9]/value}" name="property_9[]" />
            </div>
                <hr/>
            <div class="column">
                <span>Длительность урока</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{property_value[property_id=17]/value}" name="property_17[]" />
            </div>
                <hr/>
            <div class="column">
                <span>Студия</span>
            </div>
            <div class="column">
                <xsl:variable name="area_id" select="user/area_id" />

                <select class="form-control" name="areas[]" >
                    <option value="0">...</option>
                    <xsl:for-each select="areas">
                        <option value="{id}">
                            <xsl:if test="$area_id = id">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="title" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <div class="column">
                <span>Учителя</span>
            </div>
            <div class="column">
                <select class="form-control" name="property_21[]" multiple="multiple" size="3" >
                    <option value="0">...</option>
                    <xsl:call-template name="property_list" >
                        <xsl:with-param name="property_id" select="21" />
                    </xsl:call-template>
                </select>
            </div>
            <hr/>

            <div class="column">
                <span>Направление подготовки</span>
            </div>
            <div class="column">
                <select class="form-control" name="property_20[]">
                    <option value="0">...</option>
                    <xsl:call-template name="property_list" >
                        <xsl:with-param name="property_id" select="20" />
                    </xsl:call-template>
                </select>
            </div>

            <hr/>
            <div class="column">
                <span>Соглашение подписано</span>
            </div>
            <div class="column">
                <input type="checkbox" id="property_18" name="property_18[]" class="checkbox-new" >
                    <xsl:if test="property_value[property_id=18]/value = 1">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="property_18" class="label-new">
                    <div class="tick"><input type="hidden" name="kostul"/></div>
                </label>
            </div>

            <xsl:if test="user/id = ''">
                <hr style="margin: 10px 0px" />
                <div class="column get_lid_data_row">
                    <span>Данные из лида:</span>
                </div>
                <div class="column row get_lid_data_row">
                    <div class="col-lg-6"><input type="number" class="form-control" id="lid_id" placeholder="Номер лида" /></div>
                    <div class="col-lg-6"><a href="#" class="btn btn-primary" id="get_lid_data">Загрузить</a></div>
                </div>
            </xsl:if>

            <input type="hidden" name="id" value="{user/id}" />
            <input type="hidden" name="groupId" value="5" />
            <!--<input type="hidden" name="active" value="1" />-->
            <!--<input type="hidden" name="modelName" value="User" />-->
        </form>

        <button class="btn btn-default" onclick="User.saveFrom($('#createData'), User.saveClientCallback)">Сохранить</button>

        <script>
            $(".masked-phone").mask("+7(999) 999-9999");
        </script>
    </xsl:template>


    <xsl:template name="property_list">
        <xsl:param name="property_id" />

        <xsl:for-each select="//property_list[property_id=$property_id]">
            <xsl:variable name="id" select="id" />
            <option value="{$id}">
                <xsl:if test="//property_value[property_id = $property_id]/value_id = $id">
                    <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <xsl:value-of select="value" />
            </option>
        </xsl:for-each>

    </xsl:template>


</xsl:stylesheet>