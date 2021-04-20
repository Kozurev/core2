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
                    <td>
                        <div class="row">
                            <div class="col-md-4">Баланс</div>
                            <div class="col-md-1">
                                <span id="balance">
                                    <xsl:value-of select="user_balance/amount" />
                                </span>
                            </div>
                            <div class="col-md-3">
                                <xsl:if test="access_create_payment = 1 and is_admin = 1">
                                    <a class="action add_payment" onclick="makeClientPaymentPopup(0, {user/id}, saveBalancePaymentCallback)" title="Зачислить платеж">
                                        <!--Пополнить баланс-->
                                    </a>
                                </xsl:if>
<!--                                <xsl:if test="is_admin = 0 and api_token_sber != '' and has_checkout = 1">-->
<!--                                    <a onclick="Payment.getSberApi()" class="btn btn-xs btn-outline btn-primary">-->
<!--                                        Пополнить баланс-->
<!--                                    </a>-->
<!--                                </xsl:if>-->
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-4">Кол-во индив / груп занятий</div>
                            <div class="col-md-1">
                                <span>
                                    <xsl:choose>
                                        <xsl:when test="access_user_edit_lessons = 1">
                                            <span id="countLessonsIndiv"
                                                  onclick="editClientCountLessons({user/id}, User.TYPE_INDIV, '#countLessonsIndiv')"
                                                  title="Нажмите для редактирования кол-ва индивидуальных занятий">
                                                <xsl:value-of select="user_balance/individual_lessons_count" />
                                            </span>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <span id="countLessonsIndiv">
                                                <xsl:value-of select="user_balance/individual_lessons_count" />
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
                                                <xsl:value-of select="user_balance/group_lessons_count" />
                                            </span>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <span id="countLessonsGroup">
                                                <xsl:value-of select="user_balance/group_lessons_count" />
                                            </span>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <xsl:if test="access_buy_tariff = 1">
                                    <a class="action buy" title="Купить тариф" onclick="loaderOn(); Tarif.getList([], getClientLcTarifsCallBack)">
                                        <!--Купить индивидуальные занятия-->
                                    </a>
                                </xsl:if>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-4">Следующее занятие</div>
                            <div class="col-md-5">
                                <xsl:choose>
                                    <xsl:when test="count(nearest_lesson) = 1">
                                        <xsl:for-each select="nearest_lesson/lesson">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <span>
                                                        <xsl:value-of select="/root/nearest_lesson/refactoredDate" />
                                                    </span>
                                                    <xsl:text> в </xsl:text>
                                                    <span>
                                                        <xsl:value-of select="time_from" />
                                                    </span>
                                                    <br/>
                                                    <span>
                                                        <xsl:value-of select="teacher" />
                                                    </span>
                                                </div>
                                                <xsl:if test="/root/access_schedule_edit = 1">
                                                    <div class="col-md-6">
                                                        <p data-id="{id}" data-client="{/root/user/id}" data-date="{/root/nearest_lesson/date}">
                                                            <span style="margin-left: 15px">
                                                                <button class="btn btn-orange schedule_today_absent" href="#">
                                                                    <xsl:if test="/root/nearest_lesson/is_cancellable = 0">
                                                                        <xsl:attribute name="disabled">
                                                                            disabled
                                                                        </xsl:attribute>
                                                                    </xsl:if>
                                                                    Отменить занятие
                                                                </button>
                                                            </span>

                                                            <xsl:if test="/root/nearest_lesson/is_cancellable = 0 and /root/access_schedule_edit = 1 and /root/is_admin = 0">
                                                                <p style="font-size: 10px; text-align: center;">
                                                                    в автоматическом режиме можно отменить занятия не позднее чем, до 17-00 текущего дня.
                                                                    Позвоните администраторам и они всё урегулируют +79092012550
                                                                </p>
                                                            </xsl:if>
                                                        </p>
                                                        <input type="hidden" />
                                                    </div>
                                                </xsl:if>
                                            </div>
                                        </xsl:for-each>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        Занятий не найдено
                                    </xsl:otherwise>
                                </xsl:choose>
                            </div>
                            <div class="col-md-3">
                                <xsl:if test="/root/access_schedule_absent_create = 1">
                                    <div>
                                        <a class="btn btn-orange" onclick="getScheduleAbsentPopup({user/id}, 1, getCurrentDate(), '')">
                                            Период отсутствия
                                        </a>
                                    </div>
                                </xsl:if>
                            </div>
                        </div>
                    </td>
                </tr>

                <xsl:if test="/root/access_schedule_lesson_time = 1">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-12 center">
                                    <a class="btn btn-orange" onclick="makeClientLessonPopup({user/id})">
                                        Найти свободное время у преподавателя
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </xsl:if>

                <xsl:if test="count(absent) > 0 and /root/access_schedule_absent_read = 1">
                    <tr id="absent-row">
                        <td>
                            <div class="row">
                                <div class="col-md-4">Периоды отсутствия</div>
                                <div class="periods col-md-8">
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
                                            <div class="col-md-6">
                                                <xsl:if test="/root/access_schedule_absent_edit = 1">
                                                    <a class="action edit" onclick="getScheduleAbsentPopup('', '', '', {id})"><xsl:text>&#x0A;</xsl:text></a>
                                                </xsl:if>

                                                <xsl:if test="/root/access_schedule_absent_delete = 1">
                                                    <a class="action delete" onclick="deleteScheduleAbsent({id}, deleteAbsentClientCallback)"><xsl:text>&#x0A;</xsl:text></a>
                                                </xsl:if>
                                            </div>
                                        </div>
                                    </xsl:for-each>
                                </div>
                            </div>
                        </td>
                    </tr>
                </xsl:if>

                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="buttons-panel center">
<!--                                    <div>-->
<!--                                        <a class="btn btn-orange user-schedule-btn">Полное расписание</a>-->
<!--                                    </div>-->
                                    <xsl:if test="//current_user/email != '' and //my_calls_token != ''">
                                        <div>
                                            <a class="btn btn-orange" onclick="MyCalls.makeCall({//current_user/id}, '{user/phone_number}', checkResponseStatus)" title="Совершить звонок">
                                                Позвонить
                                            </a>
                                        </div>
                                    </xsl:if>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <xsl:if test="is_admin = 1">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-4">Значение медиан (индив/груп)</div>
                                <div class="col-md-3">
                                    <xsl:choose>
                                        <xsl:when test="access_user_edit_lessons = 1">
                                            <span>
                                                <span id="medianaIdiv" onclick="editClientRate({user/id}, 'client_rate_indiv', '#medianaIdiv')" title="Нажмите для корректировки медианы">
                                                    <xsl:value-of select="user_balance/individual_lessons_average_price" />
                                                </span>
                                            </span>
                                            <xsl:text> / </xsl:text>
                                            <span>
                                                <span id="medianaGroup" onclick="editClientRate({user/id}, 'client_rate_group', '#medianaGroup')" title="Нажмите для корректировки медианы">
                                                    <xsl:value-of select="user_balance/group_lessons_average_price" />
                                                </span>
                                            </span>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <span id="medianaIdiv">
                                                <xsl:value-of select="user_balance/individual_lessons_average_price" />
                                            </span>
                                            <xsl:text> / </xsl:text>
                                            <span id="medianaGroup">
                                                <xsl:value-of select="user_balance/group_lessons_average_price" />
                                            </span>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-4">Поурочно</div>
                                <div class="col-md-3">
                                    <input type="checkbox" id="per_lesson" class="checkbox-new" data-userid="{user/id}" >
                                        <xsl:if test="per_lesson = 1">
                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                        </xsl:if>
                                    </input>
                                    <label for="per_lesson" class="label-new" style="position: relative; top: 5px;">
                                        <div class="tick"><input type="hidden" name="kostul"/></div>
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <xsl:if test="entry != ''">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-4">Последяя авторизация</div>
                                    <div class="col-md-3"><xsl:value-of select="entry" /></div>
                                </div>
                            </td>
                        </tr>
                    </xsl:if>

                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-4">Статус</div>

                                <div class="col-md-6">
                                    <textarea class="form-control" placeholder="Заметки" id="client_notes" data-userid="{user/id}">
                                        <xsl:choose>
                                            <xsl:when test="user/comment != ''">
                                                <xsl:value-of select="user/comment" />
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:text>&#x0A;</xsl:text>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </textarea>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <xsl:if test="vk != ''">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-4">Ссылка ВК</div>
                                    <div class="col-md-3">
                                        <a href="{vk}" target="_blank">
                                            <xsl:value-of select="vk" />
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </xsl:if>

                    <xsl:if test="count(teachers) != 0">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-4">Преподаватель</div>
                                    <div class="col-md-8">
                                        <xsl:apply-templates select="teachers" />
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </xsl:if>

                    <xsl:if test="//current_user/email != '' and //my_calls_token != ''">
                        <tr>
                            <td class="text-center">
                                <a class="btn btn-orange" onclick="MyCalls.makeCall({//current_user/id}, '{user/phone_number}', checkResponseStatus)" title="Совершить звонок">
                                    Позвонить
                                </a>
                            </td>
                        </tr>
                    </xsl:if>

                    <xsl:if test="prev_lid != '0'">
                        <tr>
                            <td class="center">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span>Создан из лида №</span>
                                        <a href="#" class="info-by-id" data-model="Lid" data-id="{prev_lid}">
                                            <xsl:value-of select="prev_lid" />
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
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

    <xsl:template match="teachers">
        <p>
            <a href="/lk?userid={id}">
                <xsl:value-of select="surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="name" />
            </a>
        </p>
    </xsl:template>

</xsl:stylesheet>