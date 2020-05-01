<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        html {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        .list {
            margin-bottom: 20px;
        }

        .list-item {
            background-color: #ffffff;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            border-bottom: 1px solid #dddddd;
            cursor: pointer;
            display: block;
            font-family: inherit;
            font-size: inherit;
            margin: 0;
            opacity: 1;
            padding: 10px 15px;
            position: relative;
        }

        a.list-item,
        button.list-item {
            text-align: left;
            text-decoration: none;
            width: 100%;
        }

        a.list-item:hover,
        button.list-item:hover,
        a.list-item:focus,
        button.list-item:focus {
            opacity: 0.8;
            text-decoration: none;
        }

        .list-item[disabled],
        .list-item[disabled]:hover,
        .list-item[disabled]:focus {
            opacity: 0.5;
            cursor: not-allowed;
            text-decoration: none;
        }

        .container-wrapper {
            border: 1px solid blue;
            /*resize: both;*/
            /*overflow: auto;*/
            /*display: inline-block;*/
            width: 100%;
        }
    </style>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//    echo $text = "Start [:](Проекты) [:en]projects[:ro]proiecte[:fr]projets[:] - [:ro]a vecinilor mei[:en]of my neihboor[:] [:fr]aujourdouis[:] ?";
//    echo $text = "Start [:](Проекты) [:]projects[:]proiecte[:]projets[:] - [:en]of my neihboor[:] ?";
//    echo $text = "Start [:](Проекты) [:]projects[:]proiecte[:]projets[:ro]ale vecinilor[:] - [:en]of my neihboor[:] ?";
//    echo $text = "[:en]My Account[:fr]Mon compte[:ro]Contul meu[:ru]Мой счёт";
//    var_dump(preg_match('/(feed|trackback)/', '(.?.+?)/order-received(/(.*))?/?$'));
//usleep(1000000);//1sec
/*$time = (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
echo "Timp Executie: $time secunde\n";*/
/*$imgData = '<img alt="" src="http://mayfairclub.sunhouz.com/wp-content/uploads/BarberAmer.jpg" class="avatar avatar-96 photo" height="96" width="96">';
$result = preg_replace('src="(.*?)"', 'image/catalog/blank.gif', $imgData);
var_dump($result);*/
function print_constant($value){
    return $value;
}
final class MyFoo{
    const TAX_TYPE = 'type';
    public function say(){
//            echo "This is the class const value: {$this->print()}";
        $print = 'print_constant';
        echo "This is the class const value: {$print(self::TAX_TYPE)}";
    }
}
$obj = new MyFoo();
$obj->say();
?>
</body>
</html>