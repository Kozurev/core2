<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="root">
		<div class="in_main">
			<h3 class="main_title">
				<xsl:value-of select="title" />
			</h3>
			<table class="table">
				<th>id</th>
				<th>Тип</th>
				<th>Название</th>
				<th>Активность</th>
				<th>Путь</th>
				<th>Действия</th>
				<!--<th>Доп. св-ва</th>-->
				<!--<th>Редактировать</th>-->
				<!--<th>Удалить</th>-->
				<xsl:apply-templates select="structure" />
				<xsl:apply-templates select="structure_item" />
			</table>

			<button class="btn button" type="button">
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Structure&amp;parent_id={parent_id}&amp;parent_name=Structure" class="link">
					Новый раздел
				</a>
			</button>

			<button class="btn button" type="button">
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Structure_Item&amp;parent_id={parent_id}&amp;parent_name=Structure" class="link">
					Новый элемент
				</a>
			</button>

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


	<xsl:template match="structure">
		<tr>
			<td><xsl:value-of select="id" /></td>
			<td>Структура</td>

			<td class="table_structure">
				<a 
					class="link"
					href="admin?menuTab=Structure&amp;
					menuAction=show&amp;parent_id={id}&amp;parent_name=Structure">
					<xsl:value-of select="title" />
				</a>
			</td>

			<!--Активность-->
			<td>
				<input type="checkbox" class="activeCheckbox">
					<xsl:attribute name="model_name">Structure</xsl:attribute>
					<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
					<xsl:if test="active = 1">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
				</input>
			</td>

			<!--Путь-->
			<td><xsl:value-of select="path" /></td>

			<td>
				<!--Доп свойства-->
				<a href="admin?menuTab=Properties&amp;menuAction=show&amp;model_name=Structure&amp;model_id={id}" class="link propertiesLink" />
				<!--Редактирование-->
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Structure&amp;parent_id={parent_id}&amp;model_id={id}&amp;parent_name=Structure" class="link updateLink" />
				<!--Удаление-->
				<a href="admin" data-model_name="Structure" data-model_id="{id}" class="delete deleteLink"></a>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="structure_item">
		<tr>
			<td><xsl:value-of select="id" /></td>
			<td>Элемент</td>
			<td>
				<xsl:value-of select="title" />
			</td>
			
			<td>
				<input type="checkbox" class="activeCheckbox">
					<xsl:attribute name="model_name">Structure_Item</xsl:attribute>
					<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
					<xsl:if test="active = 1">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
				</input>
			</td>

			<td><xsl:value-of select="path" /></td>


			<td>
				<!--Редактирование-->
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Structure_Item&amp;parent_id={parent_id}&amp;model_id={id}&amp;parent_name=Structure" class="link updateLink" />
				<!--Удаление-->
				<a href="admin" data-model_name="Structure_Item" data-model_id="{id}" class="delete deleteLink"></a>
			</td>
		</tr>
	</xsl:template>

</xsl:stylesheet>