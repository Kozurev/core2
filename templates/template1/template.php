<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$this->title;?></title>

    <?$this
        ->css('/templates/template1/css/bootstrap.min.css')
        ->showCss()
    ?>
</head>
<body>

    <div id="container">
        <?$this->execute();?>
    </div>

</body>
</html>





