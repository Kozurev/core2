<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="col-lg-12 finances_calendar">
            Период
            с: <input type="date" class="form-control" name="date_from" value="{date_from}"/>
            по: <input type="date" class="form-control" name="date_to" value="{date_to}"/>
            <a class="btn btn-orange statistic_show" >Показать</a>
        </div>
    </xsl:template>

</xsl:stylesheet>