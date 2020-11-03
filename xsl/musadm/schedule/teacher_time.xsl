<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:include href="teacher_student.xsl" />

    <xsl:template match="root">
        <section class="section-bordered">
            <div class="row">
                <xsl:if test="//access_teacher_schedule_view = 1">
                    <div class="col-lg-6 col-sm-12 col-xs-12">
                        <h3>Основной график работы</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered teacher-schedule-main">
                                <tr>
                                    <td><span class="day-name">Понедельник</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Monday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Monday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Вторник</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Tuesday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Tuesday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Среда</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Wednesday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Wednesday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Четверг</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Thursday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Thursday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Пятница</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Friday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Friday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Суббота</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Saturday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Saturday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <tr>
                                    <td><span class="day-name">Воскресенье</span></td>
                                    <td>
                                        <div class="row">
                                            <xsl:apply-templates select="schedule_teacher[day_name = 'Sunday']" />
                                        </div>
                                        <div class="row new-time-form">
                                            <form>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_from" />
                                                </div>
                                                <div class="col-lg-5">
                                                    <input class="form-control" type="time" name="time_to" />
                                                </div>
                                                <input type="hidden" name="teacher_id" value="{user/id}" />
                                                <input type="hidden" name="day_name" value="Sunday" />
                                                <div class="col-lg-2">
                                                    <a class="action save teacher-time-save"><input type="hidden" name="kostul" /></a>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <xsl:if test="//access_teacher_schedule_delete = 1">
                                        <td>
                                            <a class="btn btn-success new-teacher-time">+</a>
                                        </td>
                                    </xsl:if>
                                </tr>
                            </table>
                        </div>
                    </div>
                </xsl:if>

                <xsl:if test="//access_teacher_clients_view = 1">
                    <div class="col-lg-6">
                        <h3>Список учеников преподавателя</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered teacher-schedule-main" id="student_table">
                                <tr>
                                    <td><span>Фамилия</span></td>
                                    <td><span>Имя</span></td>
                                    <td><span>Телефон</span></td>
                                    <xsl:if test="//access_teacher_clients_edit = 1">
                                        <td>
                                            <a class="btn btn-green" onclick="addNewStudentToTeacher({/root/user/id})"> + </a>
                                        </td>
                                    </xsl:if>
                                </tr>
                                <xsl:apply-templates select="clients"/>
                            </table>
                        </div>
                    </div>
                </xsl:if>
            </div>
        </section>
    </xsl:template>

    <xsl:template match="schedule_teacher">
        <div class="col-lg-10 time teacher-time-{id}">
            <span>
                <xsl:value-of select="timeFrom" />
                <xsl:text> - </xsl:text>
                <xsl:value-of select="timeTo" />
            </span>
        </div>

        <div class="col-lg-2 teacher-time-{id}">
            <a class="action delete" onclick="loaderOn(); Schedule.removeTeacherTime({id}, removeTeacherTimeCallback);">
                <input type="hidden" name="kostul" />
            </a>
        </div>
    </xsl:template>

    <xsl:template match="clients">
        <tr id="{id}">
            <td><xsl:value-of select="surname"/></td>
            <td><xsl:value-of select="name"/></td>
            <td><xsl:value-of select="phone_number"/></td>
            <xsl:if test="//access_teacher_clients_edit = 1">
                <td>
                    <a class="btn btn-red" onclick="User.removeClientFromTeacher({//root/user/id}, {id} ,delTeachersStudentCallback)"> - </a>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>


</xsl:stylesheet>