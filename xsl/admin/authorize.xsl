<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output xmlns="http://www.w3.org/TR/xhtml1/strict" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" encoding="utf-8" indent="yes" method="html" omit-xml-declaration="no" version="1.0" media-type="text/xml"/>


  <xsl:template match="root">
    <div class="authorize">
      <h3>Вход в систему</h3>

      <xsl:if test="error != ''">
        <span class="error">
          Ощибка авторизации
        </span>
      </xsl:if>

      <form action="/admin" method="POST">
        <span>Введите логин</span>
        <input type="text" name="login" class="form-control" />

        <span>Введите Пароль</span>
        <input type="password" name="password" class="form-control" />

        <input type="submit" class="btn btn-primary" value="Авторизация" />
      </form>
    </div>
  </xsl:template>



</xsl:stylesheet>