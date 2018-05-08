<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">
            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <xsl:if test="parent_id != 0">
                <table class="table">
                    <tr>
                        <th>id</th>
                        <th>День</th>
                        <th>Время</th>
                        <th>Учитель</th>
                        <th>Ученик</th>
                        <th>Группа</th>
                        <th>Действия</th>
                    </tr>
                    <xsl:apply-templates select="schedule_lesson" />
                </table>

                <button class="btn button" type="button">
                    <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson&amp;parent_id={parent_id}" class="link">
                        Добавить урок
                    </a>
                </button>
            </xsl:if>


            <xsl:if test="parent_id = 0">
                <table class="table">
                    <tr>
                        <th>id</th>
                        <th>Название</th>
                        <th>Кол-во классов</th>
                        <th>Действия</th>
                    </tr>
                    <xsl:apply-templates select="schedule_area" />
                </table>

                <button class="btn button" type="button">
                    <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Area&amp;parent_id={parent_id}" class="link">
                        Добавить филиал
                    </a>
                </button>
            </xsl:if>


            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Schedule&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Schedule&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="schedule_area">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td>
                <a class="link dir" href="admin?menuTab=Schedule&amp;menuAction=show&amp;parent_id={id}"><xsl:value-of select="title" /></a>
            </td>
            <td><xsl:value-of select="count_classess" /></td>
            <td>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="schedule_lesson">
        <tr>
            <td><xsl:value-of select="id" /></td>

            <td>
                <xsl:call-template name="getDayName">
                    <xsl:with-param name="eng" select="day_name" />
                </xsl:call-template>
            </td>

            <td>
                <xsl:value-of select="time_from" /> <br/>
                <xsl:value-of select="time_to" />
            </td>

            <td>
                <xsl:value-of select="teacher/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="teacher/name" />
            </td>

            <td>
                <xsl:value-of select="client/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="client/name" />
            </td>

            <td>
                <xsl:value-of select="group/title" />
            </td>

            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Schedule_Lesson&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Schedule_Lesson" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template name="getDayName">
        <xsl:param name="eng" />

        <xsl:choose>
            <xsl:when test="$eng = 'Monday'">
                Понедельник
            </xsl:when>
            <xsl:when test="$eng = 'Tuesday'">
                Вторник
            </xsl:when>
            <xsl:when test="$eng = 'Wednesday'">
                Среда
            </xsl:when>
            <xsl:when test="$eng = 'Thursday'">
                Четверг
            </xsl:when>
            <xsl:when test="$eng = 'Friday'">
                Пятница
            </xsl:when>
            <xsl:when test="$eng = 'Saturday'">
                Суббота
            </xsl:when>
            <xsl:when test="$eng = 'Sunday'">
                Воскресенье
            </xsl:when>
        </xsl:choose>

    </xsl:template>

</xsl:stylesheet>