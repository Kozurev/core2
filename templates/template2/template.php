<h2>Авторизация пользователя</h2>

<form action="/user/" method="POST">
	<p>
		Логин: <br>
		<input type="text" name="login" size="25">
	</p>
	
	<p>
		Пароль: <br>
		<input type="text" name="password" size="25">
	</p>
	
	<input type="hidden" name="login_in" value="1">
	<input type="submit" value="Авторизоваться">	
</form>

<?$this->execute();?>
