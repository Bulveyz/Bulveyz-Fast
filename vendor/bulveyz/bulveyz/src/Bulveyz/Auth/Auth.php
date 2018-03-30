<?php

namespace Bulveyz\Auth;

ob_start();

use RedBeanPHP\R;
use Bulveyz\Mailer\Mailer;

/*
 * Authorization Users
 *
 * This class contains the entire system and user authentication functions
 */

trait Auth
{
  public $errors = []; // Errors Array
  private $loadUser = null; // Load user data
  private $token = null; // Token for restore password

  // Set Guest Session and unset
  public function __construct()
  {
    if (isset($_SESSION['auth'])) {
      unset($_SESSION['guest']);
    } else {
      $_SESSION['guest'] = 1;
    }
  }

  // If user LogOut or delete all data auth from table auth
  public function userExit()
  {
    if (isset($_SESSION['auth'])) {
      $load_session = R::findOne('authorization', 'token = ?', array($_SESSION['auth']['token']));

      if ($load_session) {
        R::trash($load_session);
      }
    }

    unset($_SESSION['auth']);

    if (isset($_COOKIE['auth'])) {
      setcookie('auth','', time() - 3600 * 24 * 7);
      ob_end_flush();
    }

    redirect('/');
  }

  // Register new User 
  public function signUp()
  {
    if (isset($_POST['signUp'])) {
      // Сheck for occupancy
      if ($_POST['name'] == '' || $_POST['email'] == '' || $_POST['password'] == '' || $_POST['confirmPassword'] == '') {
        $this->errors[] = 'Fill in all the fields';
      } 

      // Check for existence username
      if (R::count('users', 'name = ?', array($_POST['name'])) > 0) {
        $this->errors[] = 'A user with this name already exists';
      } 

      // Check for existence email in table
      if (R::count('users', 'email = ?', array($_POST['email'])) > 0) {
        $this->errors[] = 'This account is already taken! Please Login';
      }

       // Check for password math
      if ($_POST['password'] != $_POST['confirmPassword']) {
        $this->errors[] = 'The passwords you entered do not match!';
      }

      // Set min symbols 8 on the password
      if (strlen($_POST['password']) < 8) {
        $this->errors[] = 'The password must contain at least 8 characters!';
      }

      // If All ok
      if (empty($this->errors)) {
        $createUser = R::dispense('users');
        $createUser->name = strip_tags($_POST['name']);
        $createUser->email = strip_tags($_POST['email']);
        $createUser->password = password_hash(strip_tags($_POST['password']), PASSWORD_DEFAULT);
        R::store($createUser);

        redirect('/login');
      } else {
        echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
      }
    }
  }

  // LogIn User
  public function signIn()
  {
    if (isset($_POST['signIn'])) {
      // Сheck for occupancy
      if ($_POST['email'] == '' || $_POST['password'] == '') {
        $this->errors[] = 'Fill in all the fields';
      } else {
        // If data > find user on email from table
        $this->loadUser = R::findOne('users', 'email = ?', array($_POST['email']));
      }

      // If user not found
      if (R::count('users', 'email = ?', array($_POST['email'])) == 0) {
        $this->errors[] = 'Account not found! You can <a href=' . '/register' . '>register new account</a>';
      }

      // Check for password math from DB
      if (!password_verify($_POST['password'], $this->loadUser['password'])) {
        $this->errors[] = 'Wrong login or password';
      }

      // If all ok
      if (empty($this->errors)) {
        $find = R::findOne('authorization', 'user_id = ?', array($this->loadUser['id']));
        if ($find) {
          R::trash($find);
        }

        $this->token = token(); // Create token

        // Write to table new row auth user
        $authorization = R::dispense('authorization');
        $authorization->user_id = $this->loadUser['id'];
        $authorization->token = $this->token;
        $authorization->time = time();

        // If row created
        if (R::store($authorization)) {
          $_SESSION['auth'] = [
              'id' => $this->loadUser['id'],
              'name' => $this->loadUser['name'],
              'password' => $this->loadUser['password'],
              'token' => $this->token
          ];

          // Check is the remember me checkbox
          if (isset($_POST['remember'])) {
            setcookie('auth', $this->token, time() + 3600 * 24 * 3);
            ob_end_flush();
          }

          redirect('/'); // Header to home page
        } else {
          exit('Error authorization'); // Exit with error
        }
      } else {
        echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
      }
    }
  }

  // Check auth User if his is in DB with session
  public function checkAuthWithSession()
  {
    $loadSession = R::findOne('authorization', 'token = ?', array($_SESSION['auth']['token']));

    if (!$loadSession) {
      $this->userExit();
    }
  }

  // Check auth User with Cookies (if isset remember me)
  public function checkAuthWithCookie()
  {
    $loadSession = R::findOne('authorization', 'token = ?', array($_COOKIE['auth']));

    if ($loadSession) {
      $loadUser = R::load('users', $loadSession['user_id']);

      $_SESSION['auth'] = [
          'id' => $loadUser['id'],
          'name' => $loadUser['name'],
          'password' => $loadUser['password'],
          'token' => $_COOKIE['auth']
      ];
    } else {
     $this->userExit();
    }
  }

  // Reset password verify email
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

        $link = siteURL()."restore/".$_SESSION['reset']['token'];
        Mailer::smtpStart();
        Mailer::$mail->addAddress($_POST['email']);    
        Mailer::$mail->isHTML(true);
        Mailer::$mail->Subject = 'Password recovery';
        Mailer::$mail->Body = "<a href='{$link}'>Password recovery link</a>";
        Mailer::$mail->setFrom(getenv('SMTP_USER_NAME'), 'Password recovery');
        Mailer::$mail->send();

        echo "<div class='alert alert-success' role='alert'>A password recovery link has been sent to the email</div>";
      } else {
        echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
      }
    }
  }

  // Restore Password (If veryfed email)
  public function resetPassword($token)
  {
    if (isset($_SESSION['reset']) &&  $_SESSION['reset']['token'] == $token) {
      if (isset($_POST['resetPassword'])) {
        if ($_POST['password'] == '') {
          $this->errors[] = 'Input new password!';
        }
        if ($_POST['password'] != $_POST['password2']) {
          $this->errors[] = 'The passwords you entered do not match!';
        }
        if (strlen($_POST['password']) < 8) {
          $this->errors[] = 'The password must contain at least 8 characters!';
        }
        if (empty($this->errors)) {
          $changeData = R::findOne('users', 'email = ?', array($_SESSION['reset']['email']));
          $changeData->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

          if (R::store($changeData)) {
            $loadAuthSession = R::findAll('authorization', 'user_id = ?', array($changeData['id']));

            foreach ($loadAuthSession as $load)
            {
              R::trash('authorization', $load['id']);
            }

            mail($_POST['email'], 'Reset Password', "Your password will be restored", 'BulveyzTeam');

            unset($_SESSION['reset']);
            redirect('/login');
          } else {
            exit('Error DataChange');
          }
        } else {
          echo "<div class='alert alert-danger' role='alert'>" . array_shift($this->errors) . "</div>";
        }
      }
    } else {
      exit('404');
    }
  }

  // Get User data (If isset auth)
  public static function user()
  {
    if (isset($_SESSION['auth'])) {
      return R::load('users', $_SESSION['auth']['id']);
    }
  }

  // Setter Check Session Auth
  public function authorization()
  {
     if (isset($_SESSION['auth'])) {
      $this->checkAuthWithSession();
    } elseif (isset($_COOKIE['auth'])) {
      $this->checkAuthWithCookie();
    }
  }
}