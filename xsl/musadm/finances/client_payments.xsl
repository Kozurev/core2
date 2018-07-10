<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

        <div class="finances_calendar">
            Период
            с: <input type="date" class="form-control" name="date_from" value="{date_from}"/>
            по: <input type="date" class="form-control" name="date_to" value="{date_to}"/>
            <a class="btn btn-green finances_show" >Показать</a>
        </div>

        <div class="finances_add_payment">
            <a class="btn btn-green finances_payment">Хозрасходы</a>
        </div>
        <br/>

        <div class="finances_total">
            За данный период суммарные поступления составили <xsl:value-of select="total_summ" /> руб.
        </div>

        <table id="sortingTable" class="table table-striped task">
            <thead>
                <tr class="header">
                    <th>№</th>
                    <th>ФИО</th>
                    <th>Сумма</th>
                    <th>Примечание</th>
                    <th>Дата</th>
                    <th>Студия</th>
                </tr>
            </thead>

            <tbody>
                <xsl:apply-templates select="payment" />
            </tbody>
        </table>
    </xsl:template>


    <xsl:template match="payment">
        <tr>
            <td><xsl:value-of select="position()" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="user/surname != ''">
                        <xsl:value-of select="user/surname" />
                        <xsl:text>  </xsl:text>
                        <xsl:value-of select="user/name" />
                    </xsl:when>
                    <xsl:otherwise>
                        Хозрасходы
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td><xsl:value-of select="value" /></td>
            <td><xsl:value-of select="description" /></td>
            <td><xsl:value-of select="datetime" /></td>
            <td><xsl:value-of select="area" /></td>
        </tr>
    </xsl:template>


</xsl:stylesheet>