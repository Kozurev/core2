<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section class="user-payments section-bordered">
            <h3>История жизни</h3>
                    <div class="balance-payments tab">
                        <table id="sortingTable" class="table table-statused">
                            <thead>
                                <tr class="header">
                                    <th>Дней отходил</th>
                                    <th>Занятий отходил</th>
                                    <th>Денег принес</th>
                                    <th>Кешбэк</th>
                                </tr>
                            </thead>

                            <tbody>
                                <td> <xsl:value-of select="life_days"/></td>
                                <td> <xsl:value-of select="count_lesson"/></td>
                                <td> <xsl:value-of select="money"/></td>
                                <td> <xsl:value-of select="cashBack"/></td>
                            </tbody>
                        </table>
                    </div>
        </section>
    </xsl:template>


</xsl:stylesheet>