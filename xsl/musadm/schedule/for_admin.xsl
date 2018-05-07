<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div id="main" class="table">
            <div class="tr">
                <xsl:apply-templates select="class" />
            </div>
        </div>
    </xsl:template>


    <xsl:template match="class">
        <div class="column td head">
            <div class="class">КЛАСС <xsl:value-of select="position()" /></div>
            <div class="td first">
                <div class="tr head">Время</div>
            </div>
            <div class="td second">
                <div class="tr head">Основное</div>
            </div>
            <div class="td third">
                <div class="tr head">Текущее</div>
            </div>

            <div class="tr">
                <div class="td time first">
                    10:00 <br/> 10:55
                </div>
                <div class="td lesson second">
                    преп. Препод Препод <br/>
                    Ученик Ученик
                </div>
                <div class="td lesson third">
                    преп. Препод Препод <br/>
                    Ученик Ученик
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>