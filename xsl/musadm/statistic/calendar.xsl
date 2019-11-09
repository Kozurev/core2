<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row finances-calendar">
            <div class="right">
                <h4>Период с:</h4>
            </div>

            <div>
                <input type="date" class="form-control" name="date_from" value="{date_from}"/>
            </div>

            <div class="right">
                <h4>по:</h4>
            </div>

            <div>
                <input type="date" class="form-control" name="date_to" value="{date_to}"/>
            </div>

            <div>
                <a class="btn btn-orange statistic_show" >Показать</a>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>