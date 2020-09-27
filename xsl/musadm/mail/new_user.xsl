<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">
        <html>
            <head>
                <title>
                    Регистрация в сервисе <xsl:value-of select="service_name" />
                </title>
            </head>
            <body>
                <h1>Здравствуйте
                    <xsl:value-of select="user/surname" />
                    <xsl:text> </xsl:text>
                    <xsl:value-of select="user/name" />
                </h1>
                <p>Вы были зарегистрированы в сервисе <a href="{wwwroot}" target="_blank"><xsl:value-of select="service_name" /></a></p>
                <p>
                    <xsl:choose>
                        <xsl:when test="password != ''">
                            Данные для входа: <br/>
                            Ссылка на <a href="{auth_link}">личный кабинет</a><br/>
                            Email: <xsl:value-of select="user/email" /><br/>
                            <xsl:if test="user/login != ''">
                                Логин: <xsl:value-of select="user/login" /><br/>
                            </xsl:if>
                            Пароль: <xsl:value-of select="password" />
                        </xsl:when>
                        <xsl:otherwise>
                            Для входа в личный кабинет перейдите по <a href="{auth_link}">ссылке</a>
                        </xsl:otherwise>
                    </xsl:choose>
                </p>
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>