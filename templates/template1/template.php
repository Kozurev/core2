<html>
<head>
	<title><?$this->title;?></title>
	<meta charset="utf-8">
	<meta name="title" content="<?=$this->meta_title;?>">
	<meta name="description" content="<?=$this->meta_description;?>">
	<meta name="keywords" content="<?=$this->meta_keywords;?>">
	<?$this
		->css('/templates/template1/css/bootstrap.min.css')
		->showCss()
	?>
</head>

<body>
	<div class="container">
		<h2><?=$this->oStructure->title();?></h2>
		<h3><?=$this->oStructure->description();?></h3>
		<?$this->execute();?>
	</div>
</body>
	<?$this
		->js('/templates/template1/js/jquery.min.js')
		->showJs();
	?>
</html>