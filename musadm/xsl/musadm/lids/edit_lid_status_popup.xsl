<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <xsl:variable name="itemClass" select="lid_status/item_class" />
        <xsl:variable name="id" select="id" />

        <form name="createData" id="createData" action=".">
            <div class="column">
                <span>Название</span><span style="color:red" >*</span>
            </div>
            <div class="column">
                <input class="form-control" type="text" value="{lid_status/title}" name="title" />
            </div>
            <hr/>

            <div class="column">
                <span>Цвет</span>
            </div>
            <div class="column">
                <select class="form-control" name="item_class" >
                    <xsl:for-each select="color">
                        <option value="{class}">
                            <xsl:if test="class = $itemClass">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="name" />
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <hr/>

            <!--<div class="column">-->
                <!--<span>Устанавливать после <xsl:value-of select="property[id = 51]/description" /></span>-->
            <!--</div>-->
            <!--<div class="column">-->
                <!--&lt;!&ndash;<input type="checkbox" class="checkbox-new" name="lid_status_consult" id="lid_status_consult" >&ndash;&gt;-->
                    <!--&lt;!&ndash;<xsl:if test="lid_status_consult = $id">&ndash;&gt;-->
                        <!--&lt;!&ndash;<xsl:attribute name="checked">checked</xsl:attribute>&ndash;&gt;-->
                    <!--&lt;!&ndash;</xsl:if>&ndash;&gt;-->
                <!--&lt;!&ndash;</input>&ndash;&gt;-->
                <!--&lt;!&ndash;<label for="lid_status_consult" class="label-new"><div class="tick">&lt;!&ndash;&ndash;&gt;</div></label>&ndash;&gt;-->
                <!--<input class="checkbox" id="lid_status_consult" type="checkbox" name="lid_status_consult" >-->
                    <!--<xsl:if test="lid_status_consult = $id">-->
                        <!--<xsl:attribute name="checked">checked</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->
                <!--<label for="lid_status_consult" class="checkbox-label">-->
                    <!--<span class="off">Нет</span>-->
                    <!--<span class="on">Да</span>-->
                <!--</label>-->
            <!--</div>-->
            <!--<hr/>-->

            <!--<div class="column">-->
                <!--<span>Устанавливать после <xsl:value-of select="property[id = 52]/description" /></span>-->
            <!--</div>-->
            <!--<div class="column">-->
                <!--&lt;!&ndash;<input type="checkbox" class="checkbox-new" name="lid_status_consult_attended" id="lid_status_consult_attended" >&ndash;&gt;-->
                    <!--&lt;!&ndash;<xsl:if test="lid_status_consult = $id">&ndash;&gt;-->
                        <!--&lt;!&ndash;<xsl:attribute name="checked">checked</xsl:attribute>&ndash;&gt;-->
                    <!--&lt;!&ndash;</xsl:if>&ndash;&gt;-->
                <!--&lt;!&ndash;</input>&ndash;&gt;-->
                <!--&lt;!&ndash;<label for="lid_status_consult_attended" class="label-new"><div class="tick">&lt;!&ndash;&ndash;&gt;</div></label>&ndash;&gt;-->
                <!--<input class="checkbox" id="lid_status_consult_attended" type="checkbox" name="lid_status_consult_attended" >-->
                    <!--<xsl:if test="lid_status_consult_attended = $id">-->
                        <!--<xsl:attribute name="checked">checked</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->
                <!--<label for="lid_status_consult_attended" class="checkbox-label">-->
                    <!--<span class="off">Нет</span>-->
                    <!--<span class="on">Да</span>-->
                <!--</label>-->
            <!--</div>-->
            <!--<hr/>-->

            <!--<div class="column">-->
                <!--<span>Устанавливать после <xsl:value-of select="property[id = 53]/description" /></span>-->
            <!--</div>-->
            <!--<div class="column">-->
                <!--&lt;!&ndash;<input type="checkbox" class="checkbox-new" name="lid_status_consult_absent" id="lid_status_consult_absent" >&ndash;&gt;-->
                    <!--&lt;!&ndash;<xsl:if test="lid_status_consult = $id">&ndash;&gt;-->
                        <!--&lt;!&ndash;<xsl:attribute name="checked">checked</xsl:attribute>&ndash;&gt;-->
                    <!--&lt;!&ndash;</xsl:if>&ndash;&gt;-->
                <!--&lt;!&ndash;</input>&ndash;&gt;-->
                <!--&lt;!&ndash;<label for="lid_status_consult_absent" class="label-new"><div class="tick">&lt;!&ndash;&ndash;&gt;</div></label>&ndash;&gt;-->
                <!--<input class="checkbox" id="lid_status_consult_absent" type="checkbox" name="lid_status_consult_absent" >-->
                    <!--<xsl:if test="lid_status_consult_absent = $id">-->
                        <!--<xsl:attribute name="checked">checked</xsl:attribute>-->
                    <!--</xsl:if>-->
                <!--</input>-->
                <!--<label for="lid_status_consult_absent" class="checkbox-label">-->
                    <!--<span class="off">Нет</span>-->
                    <!--<span class="on">Да</span>-->
                <!--</label>-->
            <!--</div>-->
            <!--<hr/>-->

            <input type="hidden" name="id" value="{lid_status/id}" />
            <!--<input type="hidden" id="director" value="{user/id}" />-->

            <button class="lid_status_submit btn btn-default">Сохранить</button>
        </form>

    </xsl:template>

</xsl:stylesheet>