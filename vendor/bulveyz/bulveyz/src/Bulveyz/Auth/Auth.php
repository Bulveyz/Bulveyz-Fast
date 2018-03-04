<?php

namespace Bulveyz\Auth;

ob_start();

use RedBeanPHP\R;

class Auth
{
  private $userName = null;
  private $userEmail = null;
  private $userPassword = null;
  private $userPassword2 = null;
  private $userToken = null;
  private $loadUser = [];
  public $errors = [];

  /**
   * Auth constructor.
   *
   * Create if user logged and destroy session else
   */
  public function __construct()
  {
    if(!isset($_SESSION))
    {
      session_start();
    }
    if (!isset($_SESSION['auth'])) {
      $_SESSION['guest'] = 1;
    }
    if (isset($_SESSION['auth'])) {
      unset($_SESSION['guest']);
    }
  }

  /**
   * @param $token
   *
   * User Exit
   *
   * Realize logout and delete all data for table auth
   * in database
   */
  public function userExit($token)
  {
    $load_session = R::findOne('auth', 'token = ?', array($token));
    if ($load_session) {
      R::trash($load_session);
    }

    unset($_SESSION['auth']);

    if (isset($_COOKIE['idSession'])) {
      setcookie('idSession','', time() - 3600 * 24 * 7);
      ob_end_flush();
    }

    redirect('/');
  }

  /**
   * SignUp
   *
   * Realize sign up system
   */
  public function signUp()
  {
    if ($_POST['login'] == '' || $_POST['email'] == '' || $_POST['password'] == '' || $_POST['password2'] == '') {
      $this->errors[] = 'Fill in all the fields!';
    }
    else {
      $this->userName = htmlspecialchars($_POST['login']);
      $this->userEmail = htmlspecialchars($_POST['email']);
      $this->userPassword = htmlspecialchars($_POST['password']);
      $this->userPassword2 = htmlspecialchars($_POST['password2']);
    }
    if (R::count('users', 'name = ?', array($_POST['login'])) > 0) {
      $this->errors[] = 'This name is already taken!';
    }
    if (R::count('users', 'email = ?', array($this->userEmail)) > 0) {
      $this->errors[] = 'This account is already taken! Please Login';
    }
    if ($this->userPassword != $this->userPassword2) {
      $this->errors[] = 'The passwords you entered do not match!';
    }
    if (strlen($this->userPassword) < 8) {
      $this->errors[] = 'The password must contain at least 8 characters!';
    }
    if (empty($this->errors)) {

      $addUser = R::dispense('users');
      $addUser->name = $this->userName;
      $addUser->email = $this->userEmail;
      $addUser->password = password_hash($this->userPassword, PASSWORD_DEFAULT);
      $addUser->time = time();
      R::store($addUser);

      redirect('login');
    } else {
      echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
    }

  }

  /**
   * Sign In Credential
   *
   * Do logIn to system user
   */
  public function signInCredential()
  {
     if ($_POST['email'] == '' || $_POST['password'] == '') {
       $this->errors[] = 'Fill in all the fields!';
     } else {
       $this->userEmail = htmlspecialchars($_POST['email']);
       $this->userPassword = htmlspecialchars($_POST['password']);
       $loadUser = R::findOne('users', 'email = ?', array($this->userEmail));
     }
    if ($this->loadUser) {
      if (!password_verify($this->userPassword, $this->loadUser['password'])) {
        $this->errors[] = 'Data Wrong!';
      }
    } else {
      $this->errors[] = 'Account not found! You can <a href='.'/register'.'>register new account</a>';
    }
    if (empty($this->errors)) {
      $this->userToken = token();
      if (isset($_POST['remember']))
      {
        setcookie('idSession',$this->userToken, time() + 3600 * 24 * 7);
        ob_end_flush();
      }

      $loginUser = R::dispense('auth');
      $loginUser->user_id = $this->loadUser['id'];
      $loginUser->token = $this->userToken;
      $loginUser->date = time();
      R::store($loginUser);

        $_SESSION['auth'] = [
            'id' => $this->loadUser['id'],
            'idSession' => $this->userToken,
            'name' => $this->loadUser['name'],
            'password' => $this->loadUser['password']
        ];
        redirect('/');

    } else {
      echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
    }
  }

  /**
   * Check Auth Session
   *
   * Check auth with isset session logged
   */
  public function checkAuthSession()
  {
    $load_session = R::findOne('auth', 'token = ?', array($_SESSION['auth']['idSession']));

    if ($load_session) {
      $loadUser = R::load('users', $load_session['user_id']);

      $_SESSION['auth'] = [
          'id' => $loadUser['id'],
          'idSession' => $_SESSION['auth']['idSession'],
          'name' => $loadUser['name'],
          'password' => $loadUser['password']
      ];
    } else {
      $this->userExit($_SESSION['auth']['idSession']);
    }
  }

  /**
   * @param $token
   *
   * Check Auth For Cookie Session
   *
   * Check auth with cookie if session logged not found
   */
  public function checkAuthForCooSession($token)
  {
    $load_session = R::findOne('auth', 'token = ?', array($token));

    if ($load_session) {
      $loadUser = R::load('users', $load_session['user_id']);

      $_SESSION['auth'] = [
          'id' => $loadUser['id'],
          'idSession' => $load_session['token'],
          'name' => $loadUser['name'],
          'password' => $loadUser['password']
      ];
    } else {
      $this->userExit($_SESSION['auth']['idSession']);
    }
  }

  /**
   * Check Auth Cookie
   */
  public function checkAuthCookie()
  {
    $load_session = R::findOne('auth', 'token = ?', array($_COOKIE['idSession']));

    if ($load_session) {
      $this->checkAuthForCooSession($_COOKIE['idSession']);
    } else {
      $this->userExit($_COOKIE['idSession']);
    }
  }

  /**
   * Request Reset
   *
   * Create session for request password reset
   */
  public function requestReset()
  {
    if (isset($_POST['requestReset'])) {
      if ($_POST['email'] == '') {
        $this->errors[] = 'Input Email!';
      }
      if (!R::count('users', 'email = ?', array($_POST['email']))) {
        $this->errors[] = 'This account not found!';
      }
      if (empty($this->errors)) {
        $_SESSION['reset'] = [
          'token' => token(),
          'email' => $_POST['email']
        ];

        mail($_POST['email'], 'Reset Password', "Your link for reset password: ".'https://bulveyz2.0.test/restore/'.$_SESSION['reset']['token']."", 'BulveyzTeam');

        echo "<div class='alert alert-success' role='alert'>A password recovery link has been sent to the email</div>";
      } else {
        echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
      }
    }
  }

  /**
   * @param $token
   *
   * Reset Password
   *
   * Restore password and delete all session auth in DB
   */
  public function resetPassword($token)
  {
    $string = preg_replace('~[^0-9]+~','', $token);
    if (isset($_SESSION['reset']) &&  $_SESSION['reset']['token'] == $string) {
      if (isset($_POST['resetPassword'])) {
        if ($_POST['password'] == '') {
          $this->errors[] = 'Input new password!';
        }
        if ($_POST['password'] != $_POST['password2']) {
          $this->errors[] = 'The passwords you entered do not match!';
        }
        if (empty($this->errors)) {
          $changeData = R::findOne('users', 'email = ?', array($_SESSION['reset']['email']));
          $changeData->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
          R::store($changeData);

          $loadAuthSession = R::findAll('auth', 'user_id = ?', array($changeData['id']));

          foreach ($loadAuthSession as $load)
          {
            R::trash('auth', $load['id']);
          }

          mail($_POST['email'], 'Reset Password', "Your password will be restored", 'BulveyzTeam');

          unset($_SESSION['reset']);
          redirect('/login');
        } else {
          echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
        }
      }
    } else {
      exit('404');
    }
  }

  /**
   * Admin LogIn
   *
   */
  public function adminLogin()
  {
    if (isset($_POST['goAdmin'])) {
      if ($_POST['admin'] == '' || $_POST['password'] == '') {
        $this->errors[] = 'Input all fields!';
      }
      $admin = R::findOne('admins', 'name = ?', array($_POST['admin']));
      if (!$admin) {
       if (!password_verify($_POST['password'], $admin['password'])) {
         $this->errors[] = 'Wrong data!';
       }
      }
      if (empty($this->errors)) {
        $key = token();
        $create = R::dispense('admina');
        $create->admin_id = $admin['id'];
        $create->key = $key;
        R::store($create);

        $_SESSION['admin'] = [
          'name' => $admin['name'],
          'key' => $key
        ];

        redirect('/panel');
      } else {
        echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
      }
    }
  }

  /**
   * @return bool
   *
   * Auth
   *
   * Return true or false if isset session logged
   */
  public static function auth()
  {
    if (isset($_SESSION['auth']) || isset($_SESSION['admin'])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @return \RedBeanPHP\OODBBean
   *
   * User
   *
   * Return user data if isset session logged
   */
  public static function user()
  {
    if (isset($_SESSION['auth'])) {
      return R::load('users', $_SESSION['auth']['id']);
    } else {
      return null;
    }
  }

  /**
   * Auth Start
   *
   * Auth staring in singleton
   */
  public function authStart()
  {
    if (isset($_POST['signUp'])) {
      $this->signUp();
    } elseif (isset($_POST['signIn'])) {
      $this->signInCredential();
    } elseif(isset($_SESSION['auth'])) {
      $this->checkAuthSession();
    } elseif(isset($_COOKIE['idSession'])) {
      $this->checkAuthCookie();
    }
  }
}