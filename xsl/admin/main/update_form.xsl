<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:exsl="http://exslt.org/common"
                extension-element-prefixes="exsl">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"
  	exclude-result-prefixes="msxml msxsl umbraco.library Exslt.ExsltCommon Exslt.ExsltDatesAndTimes Exslt.ExsltMath Exslt.ExsltRegularExpressions Exslt.ExsltStrings Exslt.ExsltSets "/>


	<xsl:template match="root">
		<xsl:variable name="modelid" select="object_id" />

		<script>
			$(function(){
				/**
				*	Обработчик для удаления поля дополнительного свойства
				*/
				$(".delete_block").on("click", function(){
					$(this).parent().remove();
				});
			});
		</script>

		<style>
			.property_field {
				width: 80% !important;
				display: inline-block;
			}

			.delete_block {
				background: url("/templates/template3/images/delete.ico");
				height: 34px;
				width: 34px;
				background-size: cover;
				display: inline-block;
				position: relative;
    			//top: 10px;
    			vertical-align: bottom;
			}

			.delete_block:hover {
				cursor: pointer;
			}

			.add_new_value {
				background-color: green;
				margin-top: 10px;
			}

			.error {
				color: red;
				border-color: red;
			}
		</style>

		<div class="in_main">
			<h3 class="main_title">
				<xsl:choose>
					<xsl:when test="/root/object_id != 0">
						Редактирование объекта
					</xsl:when>
					<xsl:otherwise>
						Создание объекта
					</xsl:otherwise>
				</xsl:choose>
			</h3>

			<script>
				$(function(){
					$("#createData").validate({
						rules: {
							<xsl:choose>
								<xsl:when test="model_name = 'Admin_Form'">
									<xsl:for-each select="admin_form[id != $modelid]">
										<xsl:if test="required = 1 or maxlength != 0">
											<xsl:value-of select="var_name"/>: {
											<xsl:if test="required = 1">
												required: true,
											</xsl:if>
											<xsl:if test="maxlength != 0">
												maxlength: <xsl:value-of select="maxlength"/>,
											</xsl:if>
											},
										</xsl:if>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
									<xsl:for-each select="admin_form">
										<xsl:if test="required = 1 or maxlength != 0">
											<xsl:value-of select="var_name"/>: {
											<xsl:if test="required = 1">
												required: true,
											</xsl:if>
											<xsl:if test="maxlength != 0">
												maxlength: <xsl:value-of select="maxlength"/>,
											</xsl:if>
											},
										</xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
						},
						messages: {
							<xsl:for-each select="admin_form">
								<xsl:if test="required = 1 or maxlength != 0">
									<xsl:value-of select="var_name"/>: {
									<xsl:if test="required = 1">
										required: "Это поле обязательно к заполнению",
									</xsl:if>
									<xsl:if test="maxlength != 0">
										maxlength: "Длинна значения данного поля не должна превышаьть <xsl:value-of select="maxlength"/> символов",
									</xsl:if>
									},
								</xsl:if>
							</xsl:for-each>
						}
					});
				});
			</script>

			<form name="createData" id="createData" action=".">
				<h3>Основные свойства</h3>

				<xsl:choose>
					<xsl:when test="model_name = 'Admin_Form'">
						<xsl:apply-templates select="admin_form[id != $modelid]" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:apply-templates select="admin_form" />
					</xsl:otherwise>
				</xsl:choose>

				<xsl:if test="/root/property/id">
					<h3>Дополнительные свойства</h3>
					<xsl:apply-templates select="/root/property" />
				</xsl:if>

				<!--id редактируемого элемента-->
				<input type="hidden" name="id">
					<xsl:choose>
						<xsl:when test="object_id != ''">
							<xsl:attribute name="value">
								<xsl:value-of select="object_id" />
							</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="value">
								0
							</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
				</input>

				<!--id родительского раздела-->
				<!-- <input type="hidden" value="{parent_id}" name="parentId" /> -->
				
				<!--Название создаваемой/редактируемой модели-->
				<input type="hidden" name="modelName" value="{/root/model_name}" />

				<button class="btn btn-success button" type="button">
					<a href="?menuTab={tab}&amp;menuAction=updateAction" class="submit">
						Сохранить
					</a>
				</button>
			</form>
		</div>
	</xsl:template>

	<!--Шаблон для формирования поля-->
	<xsl:template match="admin_form">
		<xsl:variable name="type" select="type_id" />
		<xsl:variable name="inp_type" select="../admin_form_type[id = $type]/input_type" />
		<xsl:variable name="maxlength" select="maxlength" />
		<xsl:variable name="var_name" select="var_name" />
		<xsl:variable name="value" select="value" />
		<!--<xsl:variable name="required" select="required" />-->

		<div class="form_block">
			<span><xsl:value-of select="title" /></span>

			<!--Вызов подшаблона для формирования поля формата input-->
			<xsl:if test="type_id = 1 or type_id = 2 or type_id = 3 or type_id = 6">
				<xsl:call-template name="input">
			        <xsl:with-param name="inp_type" select="$inp_type"/>
			        <xsl:with-param name="maxlength" select="$maxlength"/>
			        <xsl:with-param name="var_name" select="$var_name"/>
			        <xsl:with-param name="value" select="$value" />
			        <!--<xsl:with-param name="required" select="$required" />-->
			     </xsl:call-template>
			</xsl:if>

			<xsl:if test="type_id = 5">
				<xsl:call-template name="textarea">
			        <xsl:with-param name="maxlength" select="$maxlength"/>
			        <xsl:with-param name="var_name" select="$var_name"/>
			        <xsl:with-param name="value" select="$value" />
					<!--<xsl:with-param name="required" select="$required" />-->
			     </xsl:call-template>
			</xsl:if>

			<xsl:if test="type_id = 4">
				<xsl:call-template name="select">
			        <xsl:with-param name="var_name" select="$var_name"/>
			        <xsl:with-param name="value" select="$value" />
			     </xsl:call-template>
			</xsl:if>

		</div>
	</xsl:template>



	<!--Дополнительные свойства-->
	<xsl:template match="property">

		<xsl:variable name="id">
			<xsl:value-of select="id" />
		</xsl:variable>

		<!--Тип поля формата input-->
		<xsl:variable name="inp_type">
			<xsl:if test="type='int' or type='float'">number</xsl:if>
			<xsl:if test="type='string'">text</xsl:if>
			<xsl:if test="type='bool'">checkbox</xsl:if>
		</xsl:variable>

		<!---->
		<xsl:variable name="class_name">
			property_field
		</xsl:variable>

		<!--Название поля формы-->
		<xsl:variable name="var_name">
			<xsl:text>property_</xsl:text><xsl:value-of select="id" /><xsl:text>[]</xsl:text>
		</xsl:variable>

		<!--Максимальная длнна-->
		<xsl:variable name="maxlength">
			<xsl:choose>
				<xsl:when test="type='string'">255</xsl:when>
				<xsl:when test="type='text'">3000</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<div class="form_block">
			<span><xsl:value-of select="title" /></span><br/>

			<xsl:if test="type='int' or type='float' or type='string'">
				<xsl:for-each select="property_value">
					<xsl:variable name="value" select="value" />
					<div class="field">
						<xsl:call-template name="input">
							<xsl:with-param name="inp_type" select="$inp_type"/>
					        <xsl:with-param name="maxlength" select="$maxlength"/>
					        <xsl:with-param name="var_name" select="$var_name"/>
					        <xsl:with-param name="value" select="$value" />
					        <xsl:with-param name="class" select="$class_name" />
					     </xsl:call-template>

					     <!--Кнопка удаления свойства-->
					     <xsl:if test="position() != 1">
							<div class="delete_block"></div>
					     </xsl:if>
					</div>
				</xsl:for-each>

				<button class="add_new_value">Добавить значение</button>
			</xsl:if>

			<xsl:if test="type='list'">
				<!--id выбранного варианта в списке-->
				<xsl:variable name="selected_option_id" select="property_value/value_id" />

				<xsl:call-template name="select">
					<xsl:with-param name="var_name" select="$var_name" />
					<xsl:with-param name="value" select="$selected_option_id" />
				</xsl:call-template>
			</xsl:if>

			<xsl:if test="type='text'">
				<xsl:for-each select="property_value">
					<xsl:variable name="value" select="value" />
					<xsl:variable name="appendedClass">property_field</xsl:variable>

					<div class="field">
						<xsl:call-template name="textarea">
							<xsl:with-param name="maxlength" select="$maxlength"/>
							<xsl:with-param name="var_name" select="$var_name"/>
							<xsl:with-param name="value" select="$value" />
							<xsl:with-param name="addClass" select="$appendedClass" />
						</xsl:call-template>

						<!--Кнопка удаления свойства-->
					    <xsl:if test="position() != 1">
							<div class="delete_block"></div>
					    </xsl:if>
					</div>
				</xsl:for-each>

				<button class="add_new_value">Добавить значение</button>
			</xsl:if>

		</div>
	</xsl:template>




	<xsl:template name="input">
		<xsl:param name="inp_type" />
		<xsl:param name="maxlength" />
		<xsl:param name="var_name" />
		<xsl:param name="value" />
		<xsl:param name="class" />
		<xsl:param name="required" />

		<input type="{$inp_type}" name="{$var_name}" class="form-control {$inp_type} {$class}">
			<xsl:if test="$maxlength != 0">
				<xsl:attribute name="maxlength">
					<xsl:value-of select="$maxlength" />
				</xsl:attribute>
			</xsl:if>

			<xsl:if test="$required = '1'">
				<xsl:attribute name="required">
					required
				</xsl:attribute>
			</xsl:if>

			<xsl:if test="$inp_type = 'checkbox'">
				<xsl:if test="value = 1">
					<xsl:attribute name="checked">checked</xsl:attribute>
				</xsl:if>
			</xsl:if>
			<xsl:if test="$inp_type != 'checkbox'">
				<xsl:attribute name="value"><xsl:value-of select="$value" /></xsl:attribute>
			</xsl:if>
		</input>
	</xsl:template>


	<xsl:template name="textarea">
		<xsl:param name="maxlength" />
		<xsl:param name="var_name"/>
		<xsl:param name="value" />
		<xsl:param name="addClass" />
		<xsl:param name="required" />

		<textarea name="{$var_name}" maxlength="{$maxlength}" class="textarea {$addClass}">

			<xsl:if test="$required != ''">
				<xsl:attribute name="required">
					required
				</xsl:attribute>
			</xsl:if>

			<xsl:value-of select="$value" />
		</textarea>
	</xsl:template>


	<xsl:template name="select">
		<xsl:param name="var_name" />
		<xsl:param name="value" />

		<select name="{$var_name}" class="form-control">
			<option value="0">...</option>
			<xsl:for-each select="item">
				<xsl:variable name="id" select="id" />
				<option value="{$id}">
					<xsl:if test="$id = $value">
						<xsl:attribute name="selected">TRUE</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="title"/>
					<xsl:value-of select="value"/>
				</option>
			</xsl:for-each>
		</select>
	</xsl:template>


</xsl:stylesheet>