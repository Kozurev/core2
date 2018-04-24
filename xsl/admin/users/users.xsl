<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="root">
		<div class="in_main">
			<h3 class="main_title">
				<xsl:value-of select="title" />
			</h3>


			<style>
				.user_search_input {
				width: 85%;
				display: inline-block;
				}
				.user_search_submit {
				width: 12%;
				margin-left: 1%;
				}
				h1 {
				color: black;
				}
			</style>


			<xsl:if test="count(user_group/id) = 1">
				<div>
					<input class="form-control user_search_input" name="user_search" placeholder="Фамилия, имя" value="{search}" />
					<button class="btn button user_search_submit">Поиск</button>
				</div>
			</xsl:if>

			<xsl:if test="count(user_group/id) = 1 and count(user/id) = 0">
				<h1>По запросу "<xsl:value-of select="search" />" ничего не найдено</h1>
			</xsl:if>

			<input type="hidden" id="group_id" value="{user_group/id}" />

			<xsl:if test="count(user_group/id) &gt; 1">
				<table class="table">
					<th>id</th>
					<th>Название</th>
					<th>Путь</th>
					<th>Доп. св-ва</th>
					<th>Редактировать</th>
					<th>Удалить</th>
					<xsl:apply-templates select="user_group" />
				</table>
			</xsl:if>

			<xsl:if test="count(user/id) != 0">
				<table class="table">
					<th>id</th>
					<th>Логин</th>
					<th>Фамилия</th>
					<th>Имя</th>
					<th>Активность</th>
					<th>Редактировать</th>
					<th>Удалить</th>
					<xsl:apply-templates select="user" />
				</table>
			</xsl:if>

			<xsl:choose>
				<xsl:when test="count(user/id) = 0">
					<button class="btn button" type="button">
						<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=User_Group&amp;parent_id={group_id}" class="link">
							Создать группу
						</a>
					</button>
				</xsl:when>
				<xsl:otherwise>
					<button class="btn button" type="button">
						<a href="admin?menuTab=User&amp;menuAction=updateForm&amp;model=User&amp;parent_id={group_id}&amp;parent_name=User_Group" class="link">
							Создать пользователя
						</a>
					</button>
				</xsl:otherwise>
			</xsl:choose>

			<div class="pagination">
				<a class="prev_page" href="admin?menuTab=User&amp;action=show&amp;group_id={group_id}"></a>
				<span class="pages">Страница
					<span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
					<span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
				<a class="next_page" href="admin?menuTab=User&amp;action=show&amp;group_id={group_id}"></a>
				<span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
			</div>

		</div>
	</xsl:template>


	<xsl:template match="user_group">
		<tr>
			<td><xsl:value-of select="id"/></td>

			<td class="table_structure">
				<a class="link" href="admin?menuTab=User&amp;menuAction=show&amp;parent_id={id}&amp;parent_name=User_Group">
					<xsl:value-of select="title" />
				</a>
			</td>

			<td><xsl:value-of select="path" /></td>

			<td><a href="admin?menuTab=Properties&amp;menuAction=show&amp;model_name=User_Group&amp;model_id={id}" class="link propertiesLink" /></td>

			<!--Редактирование-->
			<td><a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=User_Group&amp;model_id={id}" class="link updateLink" /></td>

			<!--Удаление-->
			<td><a href="admin" data-model_name="User_Group" data-model_id="{id}" class="delete deleteLink"></a></td>
		</tr>
	</xsl:template>


	<xsl:template match="user">
		<tr>
			<td><xsl:value-of select="id" /></td>
			<td><xsl:value-of select="login" /></td>
			<td><xsl:value-of select="surname" /></td>
			<td><xsl:value-of select="name" /></td>

			<!--Активность-->
			<td>
				<input type="checkbox" class="activeCheckbox">
					<xsl:attribute name="model_name">User</xsl:attribute>
					<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
					<xsl:if test="active = 1">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
				</input>
			</td>

			<!--Редактирование-->
			<td><a href="admin?menuTab=User&amp;menuAction=updateForm&amp;model=User&amp;model_id={id}&amp;parent_id={group_id}&amp;parent_name=User_Group" class="link updateLink" /></td>

			<!--Удаление-->
			<td><a href="admin" data-model_name="User" data-model_id="{id}" class="delete deleteLink"></a></td>
		</tr>
	</xsl:template>


</xsl:stylesheet>