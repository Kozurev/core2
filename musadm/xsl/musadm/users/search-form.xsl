<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">
        <!--<div class="dynamic-fixed-row">-->
            <div class="row searching-row">
                <form action="." method="GET" id="search_client">
                    <div class="right col-md-1">
                        <h4>Поиск клиента</h4>
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="surname" class="form-control" placeholder="Фамилия" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="name" class="form-control" placeholder="Имя" />
                    </div>
                    <div class="col-md-2">
                        <input type="text" id="phone" class="form-control masked-phone" placeholder="Телефон" />
                    </div>
                    <input type="hidden" name="canceled" value="1" />
                    <div class="col-md-1">
                        <input type="submit" class="btn btn-green" value="Поиск" />
                    </div>
                    <div class="col-md-2">
                        <a href="#" class="btn btn-red" id="user_search_clear">Очистить</a>
                    </div>
                    <div class="col-md-2">
                        <a href="#" class="btn btn-primary user_create" data-usergroup="5">Создать</a>
                    </div>
                </form>
            </div>
        <!--</div>-->
    </xsl:template>

</xsl:stylesheet>