<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <section id="checkouts_section">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <table class="table">
                        <tr class="header">
                            <th class="text-center">#</th>
                            <th class="text-center">Название</th>
                            <th class="text-center">Тип</th>
                            <th class="text-center">Действия</th>
                        </tr>
                        <xsl:apply-templates select="checkouts" />
                    </table>
                </div>
            </div>
        </section>
    </xsl:template>

    <xsl:template match="checkouts">
        <xsl:variable name="type" select="type" />
        <tr class="text-center">
            <td><xsl:value-of select="id" /></td>
            <td><xsl:value-of select="title" /></td>
            <td><xsl:value-of select="//types[type = $type]/name" /></td>
            <td>
<!--                <a class="action edit" href="#" onclick="makeCheckoutModal({id})"></a>-->
                <a class="action associate areas_assignments" href="#" data-model-id="{id}" data-model-name="Model\Checkout\Model"></a>
<!--                <a class="action delete" onclick="deleteCheckout({id}, refreshCheckoutsTable)"></a>-->
            </td>
        </tr>
    </xsl:template>

</xsl:stylesheet>