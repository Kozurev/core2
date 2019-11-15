<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


    <xsl:template match="root">
        <section class="section-bordered">
            <form name="export_lids" id="export_lids">
                    <div class="row finances-calendar">
                        <div class="right">
                            <h4>Период с:</h4>
                        </div>

                        <div>
                            <input type="date" class="form-control" name="date_from" value="{//date_from}"/>
                        </div>
                        <div class="right">
                            <h4>по:</h4>
                        </div>

                        <div>
                            <input type="date" class="form-control" name="date_to" value="{//date_to}"/>
                        </div>

                        <div><h4>Филиал</h4></div>
                        <div>
                            <select class="form-control" name="area_id">
                                <option value="0">...</option>
                                <xsl:for-each select="schedule_area" >
                                    <xsl:variable name="areaId" select="id" />
                                    <option value="{id}"><xsl:value-of select="title" />
                                    </option>
                                </xsl:for-each>
                            </select>
                        </div>

                    </div>
                    <div class="row buttons-panel center">
                        <div>
                            <select name="status_id" class="form-control">
                                <option value="0">Статус</option>
                                <xsl:for-each select="lid_status">
                                    <xsl:variable name="id" select="id" />
                                    <option value="{id}"><xsl:value-of select="title" />
                                    </option>
                                </xsl:for-each>
                            </select>
                        </div>

                        <div>
                            <select name="instrument" class="form-control">
                                <option value="0">Инструмент</option>
                                <xsl:for-each select="property_list_values[property_id = 20]">
                                    <option value="{id}">
                                        <xsl:value-of select="value" />
                                    </option>
                                </xsl:for-each>
                            </select>
                        </div>
                        <div>
                            <div>
                                <div><h4>Поля</h4></div>
                                <div>
                                    <input type="checkbox" name="options[]" id="surname"  value="surname"/>
                                    <label for="surname">Фамилия</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="name" value="name"/>
                                    <label for="name">Имя</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="number"  value="number"/>
                                    <label for="number">Телефон</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="vk"  value="vk"/>
                                    <label for="vk">Vk-ссылка</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="date_control" value="control_date"/>
                                    <label for="date_control">Дата контроля</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="area" value="area_id"/>
                                    <label for="area">Филиал</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="status_id" value="status_id"/>
                                    <label for="status">Статус</label>
                                </div>
                                <div>
                                    <input type="checkbox" name="options[]" id="source" value="source"/>
                                    <label for="status">Источник</label>
                                </div>
                            </div>
                            <div>
                                <div><h4>Свойтсва</h4></div>
                            <div>
                                <input type="checkbox" name="properties[]" id="marker" value="54"/>
                                <label for="marker">Маркер</label>
                            </div>
                            <div>
                                <input type="checkbox" name="properties[]" id="instrument" value="20"/>
                                <label for="instrument">Инструмент</label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row buttons-panel center">
                        <div>
                            <a class="btn btn-blue" href="#" onclick="lidsExportWithFilter($('#export_lids'))">Экспорт в Excel</a>
                        </div>
                    </div>
                </form>

        </section>

    </xsl:template>

</xsl:stylesheet>