<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE xsl:stylesheet>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="root">

    	<style>
    		.form-control { margin-bottom: 10px; }
    	</style>

    	<div class="row center">
    		<div class="col-lg-4 col-md-6 col-sm-6 col-lg-offset-4">
    			<form name="changelogin" id="createData">
					<label for="login">Логин</label>
		    		<input type="text" name="login" class="form-control" value="{user/login}" />
					<label for="login">Пароль</label>
		    		<input type="password" name="pass1" class="form-control" />
		    		<input type="password" name="pass2" class="form-control" />
		    		<input type="hidden" name="id" value="{user/id}" />
		    		<input type="hidden" name="modelName" value="User" />
		    		<button class="change_login_submit btn btn-default">Сохранить изменения</button>
		    	</form>
    		</div>
    	</div>

    	
    </xsl:template>

</xsl:stylesheet>