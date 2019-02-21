<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <div class="row finances_calendar">
            <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <h4>Период с:</h4>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                <input type="date" class="form-control" name="date_from" value="{date_from}"/>
            </div>

            <div class="right col-lg-2 col-md-2 col-sm-2 col-xs-4">
                <h4>по:</h4>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-8">
                <input type="date" class="form-control" name="date_to" value="{date_to}"/>
            </div>

            <div class="col-lg-2 col-md-2 col-sm-2 col-lg-offset-1 col-md-offset-1 col-xs-12">
                <a class="btn btn-orange statistic_show" >Показать</a>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>