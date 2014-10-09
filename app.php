<?php

require_once "vendor/autoload.php";


$pdo = new PDO(
    'mysql:host=hostname;dbname=cesium_code',
    'oliveiras',
    'waterFall'
);

$app = new \Slim\Slim();

function podiumView(){

}

$app->get('/podium', 'podiumView' );

$app->get('/', function () use ($app){
    echo <<<__HTML
<!DOCTYPE html>
<html>
<head>
    <title>Cesium</title>
</head>
<body>
    <p>Por favor siga os segintes passos</p>
    <form action="/new" enctype="multipart/form-data" method="post">
        <input type="file" name="uploads[]" multiple="multiple"/><br/>
        <input type="submit" value="Upload Now"/>
    </form>
</body>
</html>
__HTML;
});

$app->post('/new', function () use ($app, $pdo){
    $jsonObj = json_decode($app->request()->getBody(), true);
    
    
    
    $student = isset($jsonObj['student']) ? $jsonObj['student'] :  'na' ;
    $problem = isset($jsonObj['problem']) ? $jsonObj['problem'] :  'na' ;
    $email = isset($jsonObj['email']) ? $jsonObj['email'] :  'na' ;
    $pontos = calcPontos();
    
    $sql = "INSERT INTO code_test (dateCreate,points,student,problem,email) VALUES (NOW(),:points,:student,:problem,:email)";
    $pdo->prepare($sql)->execute(
        array(':points' => $pontos,
              ':student' => $student,
              ':problem' => $problem,
              ':email' => $email));
    
});

function calcPontos($code) {
    if ($_FILES["file"]["error"] > 0) {
        return 0;
    } else {
        //want to save in better place?  no.... yess.. mkdir and move_uploaded_file. 
        //script .... $_FILES["file"]["tmp_name"]
    }
    
    return 0;
}



    if (!isset($_FILES['uploads'])) {
        echo "No files uploaded!!";
        return;
    }
    
    $imgs = array();
    
    $files = $_FILES['uploads'];
    
    $cnt = count($files['name']);
    for($i = 0 ; $i < $cnt ; $i++) {
          }
    
    $imageCount = count($imgs);
    if ($imageCount == 0) {
        echo 'No files uploaded!!  <p><a href="/">Try again</a>';
        return;
    }







$app->run();