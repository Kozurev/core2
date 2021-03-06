<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Фамилия</span>
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
                <input class="form-control masked-phone" type="text" value="{lid/number}" name="number"  />
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
                <select class="form-control" name="source_select" id="source_select">
                    <xsl:for-each select="//source">
                        <option value="{id}"><xsl:value-of select="value" /></option>
                    </xsl:for-each>

                    <option value="0">Другое</option>
                </select>

                <input class="form-control" type="text" value="{lid/source}" id="source_input" name="source_input" placeholder="Источник">
                    <xsl:if test="count(//source) > 0">
                        <xsl:attribute name="style">
                            display:none
                        </xsl:attribute>
                    </xsl:if>
                </input>
            </div>
            <hr/>
            <div class="column">
                <span>Смс оповещения</span>
            </div>
            <div class="column">
                <input class="checkbox" id="sms_notification" type="checkbox" name="sms_notification">
                    <xsl:if test="sms_notification = 1">
                        <xsl:attribute name="checked">true</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="sms_notification" class="checkbox-label">
                    <span class="off">Отключены</span>
                    <span class="on">Включены</span>
                </label>
            </div>
            <hr/>
            <div class="column">
                <span>Комментарий</span>
            </div>
            <div class="column">
                <textarea class="form-control" name="comment" ></textarea>
            </div>

            <button class="lid_submit btn btn-default">Сохранить</button>
        </form>

        <script>
            $(".masked-phone").mask("+79999999999");
        </script>

    </xsl:template>

</xsl:stylesheet>