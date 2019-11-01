<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$this->title;?></title>

    <?php
    $this
        ->css('/templates/template1/css/bootstrap.min.css')
        ->showCss()
    ?>

    <script src="https://www.google.com/recaptcha/api.js?hl=ru"></script>
</head>

<?php
$startYear = 2018;
if ($startYear == date('Y')) {
    $copyString = $startYear;
} else {
    $copyString = $startYear . ' - ' . date('Y');
}
?>

<body>
    <style>
        .wrapper {
            height: 100vh;
        }

        .re {
            height: 78px;
            margin-top: 10px;
            margin-left: 18px;
        }

        footer {
            width: 100%;
            /*position: absolute;*/
            height: 30px;
            margin-top: -55px;
        }

        footer .container p {
            text-align: center;
            font-size: 14px;
            margin-bottom: 2px;
        }

        footer .container p a {
            margin: 0;
            font-size: 14px;
            color: #6ec2e8;
            float: none;
        }
    </style>

    <div class="wrapper">
        <div id="container">
            <?$this->execute();?>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>При обнаружении ошибок или возникновении вопросов по системе - обратитесь к
                <a href="https://web-develop.ru.com/" target="_blank">создателю</a></p>
            <p>&#9400;<?=$copyString?></p>
        </div>
    </footer>
</body>
</html>





