<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="in_main">

            <style>
                .positive {
                background-color:palegreen !important;
                }
                .negative {
                background-color:indianred !important;
                }
                .neutral {
                background-color:lightyellow !important;
                }
                .payment_search_input {
                width: 85%;
                display: inline-block;
                }
                .payment_search_submit {
                width: 12%;
                margin-left: 1%;
                }
                h1 {
                color: black;
                }
            </style>

            <h3 class="main_title">
                <xsl:value-of select="title" />
            </h3>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Дата</th>
                    <xsl:choose>
                        <xsl:when test="model_name = 'Task'">
                            <th>Тип</th>
                            <th>Последний комментарий</th>
                        </xsl:when>
                        <xsl:otherwise>
                            <th>Автор</th>
                            <th>Текст</th>
                        </xsl:otherwise>
                    </xsl:choose>
                    <th>Действия</th>
                </tr>
                <xsl:apply-templates select="task" />
                <xsl:apply-templates select="task_note" />
            </table>

            <xsl:choose>
                <xsl:when test="model_name = 'Task'">
                    <button class="btn button" type="button">
                        <a href="admin?menuTab=Task&amp;menuAction=updateForm&amp;model={model_name}" class="link">
                            Добавить задачу
                        </a>
                    </button>
                </xsl:when>
                <xsl:otherwise>
                    <button class="btn button" type="button">
                        <a href="admin?menuTab=Task&amp;menuAction=updateForm&amp;model={model_name}&amp;parent_id={model_name}" class="link">
                            Добавить комментарий к задаче
                        </a>
                    </button>
                </xsl:otherwise>
            </xsl:choose>


            <div class="pagination">
                <a class="prev_page" href="admin?menuTab=Task&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="pages">Страница
                    <span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
                    <span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
                <a class="next_page" href="admin?menuTab=Task&amp;action=show&amp;parent_id={parent_id}"></a>
                <span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
            </div>
        </div>
    </xsl:template>


    <xsl:template match="task">
        <tr>
            <xsl:variable name="class">
                <xsl:if test="done = 1">
                    positive
                </xsl:if>
            </xsl:variable>

            <td class="{$class}"><xsl:value-of select="id" /></td>
            <td class="{$class}">
                <a class="link" href="admin?menuTab=Task&amp;menuAction=show&amp;parent_id={id}">
                    <xsl:value-of select="date" />
                </a>
            </td>
            <xsl:variable name="typeId" select="type" />
            <td class="{$class}"><xsl:value-of select="//task_type[id = $typeId]/title" /></td>
            <td class="{$class}"><xsl:value-of select="task_note/text" /></td>
            <td class="{$class}">
                <!--Редактирование-->
                <a href="admin?menuTab=Task&amp;menuAction=updateForm&amp;model=Task&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Task" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


    <xsl:template match="task_note">
        <tr>
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="date" /></td>
            <td>
                <xsl:value-of select="user/surname" />
                <xsl:text> </xsl:text>
                <xsl:value-of select="user/name" />
            </td>
            <td><xsl:value-of select="text" /></td>
            <td>
                <!--Редактирование-->
                <a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Task_Note&amp;model_id={id}" class="link updateLink" />
                <!--Удаление-->
                <a href="admin" data-model_name="Task_Note" data-model_id="{id}" class="delete deleteLink"></a>
            </td>
        </tr>
    </xsl:template>


</xsl:stylesheet>