<?php

namespace Bulveyz\Commander;

use RedBeanPHP\R;

class BCommander
{
  public function bCommander()
  {
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/');
    $twig = new \Twig_Environment($loader, array(
        'cache' => false
    ));
    echo $twig->render('commander.tmp');
  }

  public function makeController($controllerName = null)
  {
    $controllerName = ucwords($_POST['command']);

    if ($controllerName == '') {
      echo 'Controller name is empty!';
      exit();
    }

    if (!file_exists("App/Controllers/{$controllerName}Controller.php")) {
      $controllerPath = fopen("App/Controllers/{$controllerName}Controller.php", "w");
      $controllerContent = "<?php
namespace Controllers;

class {$controllerName}Controller extends Controller
{
  public function actionIndex()
  {
  
  }
  
  public function actionShow()
  {
  
  }
  
  public function actionCreate()
  {
  
  }
  
  public function actionUpdate()
  {
  
  }
  
  public function actionDelete()
  {
  
  }
}";
    fwrite($controllerPath, $controllerContent);
    fclose($controllerPath);
    } else {
      echo "Controller {$controllerName} already exists!";
    }
  }

  public function makeModel($modelName = null)
  {
    $modelName = ucwords($_POST['command']);
    if ($modelName == '') {
      echo 'Model name is empty!';
      exit();
    }
    if (!file_exists("App/Models/{$modelName}.php")) {
      $modelPath = fopen("App/Models/{$modelName}.php", "w");
      $modelContent = "<?php
namespace Model;

class {$modelName} extends Model
{
  
}";
      fwrite($modelPath, $modelContent);
      fclose($modelPath);
    } else {
      echo "Model {$modelName} already exists!";
    }
  }

  public function makeControllerAndModel()
  {
    $this->makeController($_POST['command']);
    $this->makeModel($_POST['command']);
  }

  public function makeAuth()
  {
    mkdir('templates/auth');
    copy(__DIR__ . '../../Auth/templates/login.tmp', 'templates/auth/login.tmp');
    copy(__DIR__ . '../../Auth/templates/register.tmp', 'templates/auth/register.tmp');
    copy(__DIR__ . '../../Auth/templates/reset.tmp', 'templates/auth/reset.tmp');
    copy(__DIR__ . '../../Auth/templates/restore.tmp', 'templates/auth/restore.tmp');
  }

  public function trashAll()
  {
    if ($table = R::findAll($_POST['command'])) {
      R::trashAll($table);
    } else {
      echo 'Table not found or empty!';
    }
  }

  public function newAdmin()
  {
    $data = explode(' ', $_POST['command']);

    if (count($data) == 3)
    {
      if ($data[2] == getenv('PROJECT_KEY')) {
        if (R::count('admins', 'name = ?', array($data[0]))  > 0)
        {
          echo 'This name already exists!';
        }
        else {
          $add = R::dispense('admins');
          $add->name = $data[0];
          $add->password = password_hash($data[1], PASSWORD_DEFAULT);
          R::store($add);
        }
      } else {
        echo 'Project Key incorrect!';
      }
    } else {
      echo 'Input all data!';
    }
  }
}