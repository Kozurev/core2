<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-info section-bordered">
            <div class="row">
                <h3>Общие сведения</h3>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover" cellspacing='0'>
                            <tr>
                                <td>Баланс</td>
                                <td>
                                    <span id="balance">
                                        <xsl:call-template name="property">
                                            <xsl:with-param name="id" select="'12'"/>
                                        </xsl:call-template>
                                    </span>
                                </td>
                                <td>
                                    <xsl:if test="access_create_payment = 1 and is_admin = 1">
                                        <a class="action add_payment" onclick="makeClientPaymentPopup(0, {user/id}, saveBalancePaymentCallback)" title="Зачислить платеж">
                                            <!--Пополнить баланс-->
                                        </a>
                                    </xsl:if>
                                    <xsl:if test="is_admin = 0 and api_token_sber != ''">
                                        <a onclick="Payment.getSberApi()"
                                           class="btn btn-xs btn-outline btn-primary">
                                            Пополнить баланс
                                        </a>
                                    </xsl:if>
                                </td>
                            </tr>

                            <tr>
                                <td>Кол-во индив / груп занятий</td>
                                <td>
                                    <span>
                                        <xsl:choose>
                                            <xsl:when test="access_user_edit_lessons = 1">
                                                <span id="countLessonsIndiv"
                                                      onclick="editClientCountLessons({user/id}, User.TYPE_INDIV, '#countLessonsIndiv')"
                                                      title="Нажмите для редактирования кол-ва индивидуальных занятий">
                                                    <xsl:call-template name="property">
                                                        <xsl:with-param name="id" select="'13'"/>
                                                    </xsl:call-template>
                                                </span>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <span id="countLessonsIndiv">
                                                    <xsl:call-template name="property">
                                                        <xsl:with-param name="id" select="'13'"/>
                                                    </xsl:call-template>
                                                </span>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </span>
                                    <xsl:text> / </xsl:text>
                                    <span>
                                        <xsl:choose>
                                            <xsl:when test="access_user_edit_lessons = 1">
                                                <span id="countLessonsGroup"
                                                      onclick="editClientCountLessons({user/id}, User.TYPE_GROUP, '#countLessonsGroup')"
                                                      title="Нажмите для редактирования кол-ва групповых занятий">
                                                    <xsl:call-template name="property">
                                                        <xsl:with-param name="id" select="'14'"/>
                                                    </xsl:call-template>
                                                </span>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <span id="countLessonsGroup">
                                                    <xsl:call-template name="property">
                                                        <xsl:with-param name="id" select="'14'"/>
                                                    </xsl:call-template>
                                                </span>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </span>
                                </td>
                                <td>
                                    <xsl:if test="access_buy_tarif = 1">
                                        <a class="action buy" title="Купить тариф" onclick="loaderOn(); Tarif.getList([], getClientLcTarifsCallBack)">
                                            <!--Купить индивидуальные занятия-->
                                        </a>
                                    </xsl:if>
                                </td>
                            </tr>

                            <xsl:if test="is_admin = 1">
                                <xsl:if test="is_director = 1">
                                    <tr>
                                        <td>Значение медиан (индив/груп)</td>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="access_user_edit_lessons = 1">
                                                    <span>
                                                        <span id="medianaIdiv" onclick="editClientRate({user/id}, 'client_rate_indiv', '#medianaIdiv')"
                                                              title="Нажмите для корректировки медианы">
                                                            <xsl:value-of select="mediana_indiv" />
                                                        </span>
                                                    </span>
                                                    <xsl:text> / </xsl:text>
                                                    <span>
                                                        <span id="medianaGroup" onclick="editClientRate({user/id}, 'client_rate_group', '#medianaGroup')"
                                                              title="Нажмите для корректировки медианы">
                                                            <xsl:value-of select="mediana_group" />
                                                        </span>
                                                    </span>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <span id="medianaIdiv">
                                                        <xsl:value-of select="mediana_indiv" />
                                                    </span>
                                                    <xsl:text> / </xsl:text>
                                                    <span id="medianaGroup">
                                                        <xsl:value-of select="mediana_group" />
                                                    </span>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </td>
                                        <td></td>
                                    </tr>
                                </xsl:if>

                                <tr>
                                    <td>Поурочно</td>
                                    <td colspan="2">
                                        <input type="checkbox" id="per_lesson" class="checkbox-new" data-userid="{user/id}" >
                                            <xsl:if test="per_lesson = 1">
                                                <xsl:attribute name="checked">checked</xsl:attribute>
                                            </xsl:if>
                                        </input>
                                        <label for="per_lesson" class="label-new" style="position: relative; top: 5px;">
                                            <div class="tick"><input type="hidden" name="kostul"/></div>
                                        </label>
                                    </td>
                                </tr>

                                <xsl:if test="count(absent) > 0">
                                    <tr id="absent-row">
                                        <td>Периоды отсутствия</td>

                                        <td colspan="2" class="periods">
                                            <xsl:for-each select="absent">
                                                <div class="row" data-period-id="{id}">
                                                    <div class="col-md-6">
                                                        <xsl:variable name="periodClass">
                                                            <xsl:choose>
                                                                <xsl:when test="current = 1">
                                                                    green
                                                                </xsl:when>
                                                                <xsl:otherwise>
                                                                    default
                                                                </xsl:otherwise>
                                                            </xsl:choose>
                                                        </xsl:variable>

                                                        <span id="absent-from" class="{$periodClass}"><xsl:value-of select="date_from" /></span>
                                                        <span class="{$periodClass}"> - </span>
                                                        <span id="absent-to" class="{$periodClass}"><xsl:value-of select="date_to" /></span>
                                                    </div>

                                                    <xsl:if test="/root/access_schedule_absent = 1">
                                                        <div class="col-md-6">
                                                            <a class="action edit" onclick="getScheduleAbsentPopup('', '', '', {id})"><xsl:text>&#x0A;</xsl:text></a>
                                                            <a class="action delete" onclick="deleteScheduleAbsent({id}, deleteAbsentClientCallback)"><xsl:text>&#x0A;</xsl:text></a>
                                                        </div>
                                                    </xsl:if>
                                                </div>
                                            </xsl:for-each>
                                        </td>
                                    </tr>
                                </xsl:if>

                                <xsl:if test="entry != ''">
                                    <tr>
                                        <td>Последяя авторизация</td>
                                        <td colspan="2"><xsl:value-of select="entry" /></td>
                                    </tr>
                                </xsl:if>

                                <tr>
                                    <td>Статус</td>

                                    <td colspan="2">
                                        <textarea class="form-control" placeholder="Заметки" id="client_notes" data-userid="{user/id}" >
                                            <xsl:choose>
                                                <xsl:when test="note != ''">
                                                    <xsl:value-of select="note" />
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <xsl:text>&#x0A;</xsl:text>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        <div class="row buttons-panel center">
                                            <xsl:if test="/root/access_schedule_absent = 1">
                                                <div>
                                                    <a class="btn btn-orange" onclick="getScheduleAbsentPopup({user/id}, 1, getCurrentDate(), '')">
                                                        Добавить период отсутствия
                                                    </a>
                                                </div>
                                            </xsl:if>

                                            <xsl:if test="//current_user/email != '' and //my_calls_token != ''">
                                                <a class="btn btn-orange" onclick="MyCalls.makeCall({//current_user/id}, '{client/phone_number}', checkResponseStatus)" title="Совершить звонок">
                                                    Позвонить
                                                </a>
                                            </xsl:if>
                                        </div>
                                    </td>
                                </tr>
                                <xsl:if test="prev_lid != '0'">
                                    <td colspan="3" class="center">
                                        Создан из лида №
                                        <a href="#" class="info-by-id" data-model="Lid" data-id="{prev_lid}">
                                            <xsl:value-of select="prev_lid" />
                                        </a>
                                    </td>
                                </xsl:if>
                            </xsl:if>

                        </table>
                    </div>
                </div>
            </div>
        </section>
    </xsl:template>


    <xsl:template name="property">
        <xsl:param name="id"/>

        <xsl:choose>
            <xsl:when test="property[property_id=$id]/value = ''">
                0
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="property[property_id=$id]/value" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>