<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
    	<xsl:choose>
			<xsl:when test="count(payment_tarif) = 0">
				<h3>Не найдено ни одногоо тарифа</h3>
			</xsl:when>
			<xsl:otherwise>
				<select name="tarif_id" class="form-control">
					<xsl:for-each select="payment_tarif">
						<option value="{id}">
							<xsl:value-of select="title" />
							<xsl:text> </xsl:text>
							<xsl:value-of select="price" />
							<xsl:text>р. Уроков: </xsl:text><br/>
							<xsl:text>Индив. </xsl:text><xsl:value-of select="count_indiv" />
							<xsl:text> / Групп.: </xsl:text><xsl:value-of select="count_group" />
							<!--<xsl:value-of select="lessons_count" />-->
						</option>
					</xsl:for-each>
				</select>

				<button class="popop_buy_tarif_submit btn btn-default" data-userid="{user/id}">Сохранить</button>
			</xsl:otherwise>
		</xsl:choose>
    </xsl:template>

</xsl:stylesheet>