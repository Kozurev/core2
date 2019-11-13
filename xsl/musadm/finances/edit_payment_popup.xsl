<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Сумма</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <input type="number" name="value" class="form-control" value="{payment/value}" />
            </div>
            <div class="column">
                <span>Дата</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <input type="date" name="datetime" class="form-control" value="{payment/datetime}" />
            </div>
            <xsl:if test="payment/type = 0 or payment/type > 3">
                <div class="column">
                    <span>Тип</span>
                </div>
                <div class="column">
                    <select class="form-control" name="type">
                        <xsl:apply-templates select="payment_type" />
                    </select>
                </div>
            </xsl:if>
            <div class="column">
                <span>Филиал</span>
            </div>
            <div class="column">
                <select name="areaId" class="form-control">
                    <xsl:apply-templates select="schedule_area" />
                </select>
            </div>
            <div class="column">
                <span>Примечание</span><span style="color:red">*</span>
            </div>
            <div class="column">
                <textarea name="description" class="form-control"><xsl:value-of select="payment/description" /></textarea>
            </div>

            <input type="hidden" name="id" value="{payment/id}" />
            <input type="hidden" name="modelName" value="Payment" />
            <input type="hidden" name="user" value="{payment/user}" />
            <input type="hidden" name="after_save_action" value="{afterSaveAction}" />
            <button class="popop_payment_submit btn btn-default">Сохранить</button>
        </form>
    </xsl:template>


    <xsl:template match="payment_type">
        <option value="{id}">
            <xsl:if test="id = //payment/id">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:if test="id_deletable != 1">
                <xsl:attribute name="disabled">disabled</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="title" />
        </option>
    </xsl:template>


    <xsl:template match="schedule_area">
        <xsl:if test="position() = 1">
            <option value="0"> ... </option>
        </xsl:if>

        <option value="{id}">
            <xsl:if test="//payment/area_id = id">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="title" />
        </option>
    </xsl:template>

</xsl:stylesheet>