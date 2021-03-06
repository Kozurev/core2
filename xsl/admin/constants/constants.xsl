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
				<th>Заголовок</th>
				<th>Название</th>
				<th>Активность</th>
				<th>Действия</th>
				<th>Описание</th>
				<!--<th>Редактировать</th>-->
				<!--<th>Удалить</th>-->
				<xsl:apply-templates select="constant_dir" />
				<xsl:apply-templates select="constant" />
			</table>

			<button class="btn button" type="button">
				<a 
					href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Constant_Dir&amp;parent_id={parent_id}"
					class="link">
					Создать раздел
				</a>
			</button>

			<button class="btn button" type="button">
				<a 
					href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Constant&amp;parent_id={parent_id}&amp;parent_name=Constant_Dir"
					class="link">
					Создать константу
				</a>
			</button>

			<div class="pagination">
				<a class="prev_page" href="admin?menuTab=Constant&amp;action=show&amp;parent_id={parent_id}"></a>
				<span class="pages">Страница
					<span id="current_page"><xsl:value-of select="pagination/current_page" /></span> из
					<span id="count_pages"><xsl:value-of select="pagination/count_pages" /></span></span>
				<a class="next_page" href="admin?menuTab=Constant&amp;action=show&amp;parent_id={parent_id}"></a>
				<span class="total_count">Всего элементов: <xsl:value-of select="pagination/total_count"/></span>
			</div>
		</div>


	</xsl:template>


	<xsl:template match="constant_dir">
		<tr>
			<!--id-->
			<td><xsl:value-of select="id" /></td>

			<!--Заголовок-->
			<td class="table_structure">
				<a 
					class="link dir"
					href="admin?menuTab=Constant&amp;
					menuAction=show&amp;parent_id={id}&amp;parent_name=Constant_Dir">
					<xsl:value-of select="title" />
				</a>
			</td>

			<!--Название-->
			<td></td>

			<!--Описание-->
			<td><xsl:value-of select="description" /></td>

			<!--Активность-->
			<td></td>


			<td>
				<!--Редактирование-->
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Constant_Dir&amp;parent_id={parent_id}&amp;model_id={id}" class="link updateLink" />
				<!--Удаление-->
				<a href="admin" data-model_name="Constant_Dir" data-model_id="{id}" class="delete deleteLink"></a>
			</td>
		</tr>
	</xsl:template>


	<xsl:template match="constant">
		<tr>
			<!--id-->
			<td><xsl:value-of select="id" /></td>
			
			<!--Заголовок-->
			<td><xsl:value-of select="title" /></td>
			
			<!--Название-->
			<td><xsl:value-of select="name" /></td>

			<!--Описание-->
			<td><xsl:value-of select="description" /></td>

			<!--Активность-->
			<td>
				<input type="checkbox" class="activeCheckbox">
					<xsl:attribute name="model_name">Constant</xsl:attribute>
					<xsl:attribute name="model_id"><xsl:value-of select="id" /></xsl:attribute>
					<xsl:if test="active = 1">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
				</input>
			</td>

			<td>
				<!--Редактирование-->
				<a href="admin?menuTab=Main&amp;menuAction=updateForm&amp;model=Constant&amp;parent_id={dir}&amp;model_id={id}" class="link updateLink" />
				<!--Удаление-->
				<a href="admin" data-model_name="Constant" data-model_id="{id}" class="delete deleteLink"></a>
			</td>
		</tr>
	</xsl:template>

</xsl:stylesheet>