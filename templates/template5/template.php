<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$this->title;?></title>

    <?$this
        ->css('/templates/template5/css/bootstrap.min.css')
        ->showCss()
    ?>
</head>
<body>

    <div class="container">
        <h2>Пустой макет</h2>
        <?$this->execute();?>
    </div>

    <?$this
        ->js("/templates/template5/js/jquery.min.js")
        ->showJs();
    ?>

</body>
</html>





