<?php
/** This file only contains API's
 *  Contais APIS by this order:
 *  /podim/<problemId> GET
 *  /problem        GET all problems
 *  /problem        POST <Admin> add problem
 *  /problem/<id>   GET problem ids
 *  /problem/<id>   PUT <Admin> edit problem
 *  /problem/<id>/podium GET actual result
 *  /problem/<id>/pdf GET pdf
 *  /problem/<id>/submission POST add new submission
 * 
 */
/**
 * Pretendido:

Admin
Na dashboard, o admin pode criar um problema fazendo upload do enunciado (pdf), script que atribui pontuação, definir hora limite.
A página do problema deve ter dois rankings: o competitivo (qualquer submissão antes da hora limite deve aparecer neste ranking) e o pós-competição (após a hora limite deve aparecer neste ranking)
Listar problemas colocando uma label de "A decorrer" ou "Terminado"
*/
namespace App;
require_once "../vendor/autoload.php";

use \Slim\Slim;
use \PDO;

$app = new \Slim\Slim();

Class SimplePDO extends \PDO {
    public function __construct($dsn, $username, $password ) {
        parent::__construct($dsn,$username, $password);
    }

    public function getAll($queryStr, $tokens = [], $fetch_style = PDO::FETCH_ASSOC)
    {
        $statement = parent::prepare($queryStr); 
        $statement->execute($tokens);
        return $statement->fetchAll($fetch_style);
    }

    public function getOne($queryStr, $tokens = [], $fetch_style = PDO::FETCH_ASSOC)
    {
        $statement = parent::prepare($queryStr);
        $statement->execute($tokens);
        if ($res = $statement->fetch($fetch_style)){
            return $res;
        } else {
            return null;
        }
    }

    public function put($insertStr, $tokens = [])
    {
        $sql = "INSERT INTO problem(name, end_date) VALUES(:name, :end_date)";
        $pdo->prepare($sql)->execute($tokens);
    }

    public function putGetId($insertStr, $tokens = [])
    {
        $this->put($insertStr, $tokens);
        return $pdo->lastInsertId();
    }
}

$pdo = new SimplePDO(
    'mysql:host=localhost;dbname=dpum',
    'oliveiras',
    'waterFall'
);

$app->get('/login' ,function () use ($app, $pdo){
    session_start();
    $_SESSION['logged'] = true;    
});

$app->get('/logout' ,function () use ($app, $pdo){
    session_start();
    $_SESSION['logged'] = false;    
});

$auth = function() use ($app){
    session_start();
    if ($_SESSION['logged'] !== true){
        $app->halt(403);
    }
};

$app->get('/problem', function () use ($app, $pdo){
    echo json_encode($pdo->getAll("SELECT * from guest_problem"));
});

$app->get('/problem/:id', function ($id) use ($app, $pdo){
    echo json_encode($pdo->getOne(
        "SELECT * from guest_problem where id = :id",
        [':id' => $id]));
});

$app->post('/problem/:id', function ($id) use ($app, $pdo){
    //get 
    $problem_id = $id;
    $student_code = isset($_POST['studentCode']) ? $_POST['studentCode'] :  'na' ;
    $student_name = isset($_POST['studentName']) ? $_POST['studentName'] :  'na' ;
    $student_email = isset($_POST['studentEmail']) ? $_POST['studentEmail'] :  'na' ;
    //calculate your points
    if ($_FILES["output"]["error"] <= 0) {
        $cmd = '/usr/bin/perl '.__DIR__.'/../upload/scripts/'.$problem_id.'.pl '
                               .__DIR__.'../upload/scripts/'.$problem_id.'.in '
                               .$_FILES["code"]["output"].' 2>&1';
        set_time_limit(0);
        $points = (int) shell_exec($cmd);
    } else {
        $points = 0;
    }
    // put your submission data
    $idNew = $pdo->putGetId(
       'INSERT INTO submission(problem_id, student_code, student_name, student_email, create_date, points)
        VALUES (:problem_id, :student_code, :student_name, :student_email, now(), :points)',
        [':problem_id' => $problem_id,
        ':student_code' => $student_code,
        ':student_name' => $student_name,
        ':student_email' => $student_email,
        ':dateCreate' => $dateCreate,
        ':points' => $points]);
    //store your files:
    $path = __DIR__.'/../upload/'.$problem_id.'/'.$idNew;
    if ($_FILES["code"]["error"] <= 0) {
        move_uploaded_file($_FILES["code"]['tmp_name'],$path.".code" );
    }

    if ($_FILES["output"]["error"] <= 0) {
        move_uploaded_file($_FILES["output"]['tmp_name'],$path.".out" );
    }  

    if ($_FILES["pdf"]["error"] <= 0) {
       move_uploaded_file($_FILES["pdf"]['tmp_name'],$path.".out" );
    }
});

$app->get('/problem/:id/podium', function ($id) use ($app, $pdo){
    echo json_encode($pdo->getAll(
        "SELECT * from guest_submission where problem_id = :id order by points desc",
        [':id' => $id]));
});

$app->group('/admin', $auth, function () use ($app) {
    
    $app->post('/problem', function () use ($app, $pdo){
        //get data
        $name = isset($_POST['name']) ? $_POST['name'] :  'na' ;
        $description = isset($_POST['description']) ? $_POST['description'] :  'na' ;
        $endDate = isset($_POST['endDate']) ? $_POST['endDate'] :  'na' ;
        //put in database
        $idNew = $pdo->putGetId(
            "INSERT INTO problem(name, description, end_date) 
            VALUES(:name, :description, :end_date))",
             [':name' => $name,
              ':description' => $description,
              ':end_date' => $endDate]
        );
        //gerate paths and create
        $pathPublic = __DIR__.'/files/'.$idNew;
        $path = __DIR__.'/../upload/scripts/'.$idNew;
        $path1 = __DIR__.'/../upload/'.$idNew.'/';
        set_time_limit(0);
        shell_exec("mkdir $path1");
        //move to right place
        if ($_FILES["input"]["error"] <= 0) {
            move_uploaded_file($_FILES["script"]['tmp_name'],$path.".in" );
        }
        if ($_FILES["script"]["error"] <= 0) {
            move_uploaded_file($_FILES["script"]['tmp_name'],$path.".pl" );
        }
        if ($_FILES["pdf"]["error"] <= 0) {
            move_uploaded_file($_FILES["pdf"]['tmp_name'],$pathPublic.".pdf" );
        }
    });

    $app->put('/problem/:id', function () use ($app, $pdo){
        //get data
        $name = isset($_POST['name']) ? $_POST['name'] :  'na' ;
        $endDate = isset($_POST['endDate']) ? $_POST['endDate'] :  'na' ;
        //put in database
        $idNew = $pdo->putGetId(
            "UPDATE problem set name = :name, description = :description, end_date = :end_date",
             [':name' => $name,
              ':description' => $description,
              ':end_date' => $endDate]
        );
        //gerate paths and create
        $pathPublic = __DIR__.'/files/'.$idNew;
        $path = __DIR__.'/../upload/scripts/'.$idNew;
        //move to right place
        if ($_FILES["input"]["error"] <= 0) {
            move_uploaded_file($_FILES["script"]['tmp_name'],$path.".in" );
        }
        if ($_FILES["script"]["error"] <= 0) {
            move_uploaded_file($_FILES["script"]['tmp_name'],$path.".pl" );
        }
        if ($_FILES["pdf"]["error"] <= 0) {
            move_uploaded_file($_FILES["pdf"]['tmp_name'],$pathPublic.".pdf" );
        }
    });
});






/*
$app->post('/problem', $auth, function () use ($app, $pdo){
    $uid = uniqid();
    $name = isset($_POST['name']) ? $_POST['name'] :  'na' ;
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] :  'na' ;
    
    fileSave('problem',$uid.'.pdf','document');
    fileSave('problem',$uid.'.pl','script');

    $sql = "INSERT INTO problem(name, end_date) VALUES(:name, :end_date)";
    $pdo->prepare($sql)->execute(
    array(':name' => $name,
          ':end_date' => $endDate);

});

$app->post('/problem/:id', $auth, function ($id) use ($app, $pdo){
    
    $sql = "UPDATE problem set name = :name, end_date = :end_date where id = :id";
    $pdo->prepare($sql)->execute(
    array(':name' => $name,
          ':end_date' => $end_date
          ':id' => $id);
});

class Problem {
    protected $id;
    protected $name;
    protected $endDate;
    protected $scriptPath;
    protected $pdfPath;

    public function __construct($name, $endDate, $scriptPath, $pdfPath, $id = null){
        $this->name = $name;
        $this->endDate = $endDate;
        $this->scriptPath = $scriptPath;
        $this->pdfPath = $pdfPath;
        $this->id = $id;
    }
}

function fileSave($folderName, $fileName, $filePostName) {
    $path ='../uploads/'.$folderName.'/'.$fileName;
    //CenasX
    if ($_FILES[$filePostName]["error"] <= 0) {
        move_uploaded_file($_FILES[$filePostName]['tmp_name'],$path );
    }   
    return $path;
}


//XDEBUG_SESSION_START=ECLIPSE_DBGP&amp;KEY=14131304091982
$app->get('/podium', function () use ($app, $pdo){
    $sql = "SELECT studentName as name, studentCode as number, points as score from code order by points desc";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    //some magic to convert $r to json (....)
    echo json_encode($results);
});

$app->post('/new', function () use ($app, $pdo){
    //$jsonObj = json_decode($app->request()->getBody(), true);
    $jsonObj = $_POST; 
    
    $studentCode = isset($jsonObj['studentCode']) ? $jsonObj['studentCode'] :  'na' ;
    $studentName = isset($jsonObj['studentName']) ? $jsonObj['studentName'] :  'na' ;
    $problem = isset($jsonObj['problem']) ? $jsonObj['problem'] :  'na' ;
    $email = isset($jsonObj['email']) ? $jsonObj['email'] :  'na' ;
    $path = fileSave();
    $pontos = calcPoints($path);
    
    $sql = "INSERT INTO code (dateCreate,points,studentCode,studentName,problem,email,path) VALUES (NOW(),:points,:studentCode,:studentName,:problem,:email,:path)";
    $pdo->prepare($sql)->execute(
        array(':points' => $pontos,
              ':studentCode' => $studentCode,
              ':studentName' => $studentName,
              ':problem' => $problem,
              ':email' => $email,
              ':path' => $path));
    
    $app->response->redirect('../index.html', 303);
});

function file2Save() {
    $uploads_dir = 'uploads';
    $path = $uploads_dir.'/'.uniqid();
    //CenasX
    if ($_FILES["code"]["error"] <= 0) {
        move_uploaded_file($_FILES["code"]['tmp_name'],$path.".code" );
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
    $cmd = '/usr/bin/perl '.__DIR__.'/verify.pl '.__DIR__.'/t20.in '.__DIR__.'/'.$filePrefix.'.out 2>&1';
    set_time_limit(0);
    $result = shell_exec($cmd);
    //want to save in better place?  no.... yess.. mkdir and move_uploaded_file.
    //script .... $_FILES["file"]["tmp_name"]
    return (int) $result;
}
*/

$app->run();