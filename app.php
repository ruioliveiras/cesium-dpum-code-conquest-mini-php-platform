<?php

require_once "vendor/autoload.php";

$app = new \Slim\Slim();
$pdo = new PDO(
    'mysql:host=localhost;dbname=cesium_code',
    'oliveiras',
    'waterFall'
);

//XDEBUG_SESSION_START=ECLIPSE_DBGP&amp;KEY=14131304091982
$app->get('/podium', function () use ($app, $pdo){
    $sql = "SELECT student as number, points as score from code order by dateCreate";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    //some magic to convert $r to json (....)
    echo json_encode($results);
});

$app->post('/new', function () use ($app, $pdo){
    //$jsonObj = json_decode($app->request()->getBody(), true);
    $jsonObj = $_POST; 
    
    $student = isset($jsonObj['student']) ? $jsonObj['student'] :  'na' ;
    $problem = isset($jsonObj['problem']) ? $jsonObj['problem'] :  'na' ;
    $email = isset($jsonObj['email']) ? $jsonObj['email'] :  'na' ;
    $path = fileSave();
    $pontos = calcPoints($path);
    
    $sql = "INSERT INTO code (dateCreate,points,student,problem,email,path) VALUES (NOW(),:points,:student,:problem,:email,:path)";
    $pdo->prepare($sql)->execute(
        array(':points' => $pontos,
              ':student' => $student,
              ':problem' => $problem,
              ':email' => $email,
              ':path' => $path));
    
});

function fileSave() {
    $uploads_dir = 'uploads';
    $path = $uploads_dir.'/'.uniqid();
    //CenasX
    if ($_FILES["codigo"]["error"] <= 0) {
        move_uploaded_file($_FILES["codigo"]['tmp_name'],$path.".code" );
    }

    if ($_FILES["output"]["error"] <= 0) {
        move_uploaded_file($_FILES["output"]['tmp_name'],$path.".out" );
    }
    
    if ($_FILES["pdf"]["error"] <= 0) {
        move_uploaded_file($_FILES["pdf"]['tmp_name'],$path.".pdf" );
    }
    
    return $path;
}

function calcPoints($filePrefix) 
{
    $result = shell_exec('perl verify.pl '.$filePrefix.'.out t20.in');
    //want to save in better place?  no.... yess.. mkdir and move_uploaded_file.
    //script .... $_FILES["file"]["tmp_name"]
    return (int) $result;
}


$app->run();