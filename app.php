<?php

require_once "vendor/autoload.php";

$app = new \Slim\Slim();
$pdo = new PDO(
    'mysql:host=localhost;dbname=cesium_code',
    'oliveiras',
    'waterFall'
);


$app->get('/podium', function () use ($app, $pdo){
    $sql = "SELECT * from code_test";
    $r = $pdo->prepare($sql)->execute();
    
    //some magic to convert $r to json (....)
    
    echo $json;
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

$app->run();