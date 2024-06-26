<?php
include_once '../model/login.model.php';
include_once '../model/model.php';

if(session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }

//if the user pressed the button to log out
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');  // Redirect to login page after logging out
    exit();
}

// If user is already logged in, redirect to home page
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['admin']) && $_SESSION['admin'] === true || isset($_SESSION['prof']) && $_SESSION['prof'] === true) {
    header('Location: home.php');
    exit();
}else if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['alumno']) && $_SESSION['alumno'] === true){
    header('Location: alumnes.activitats.php');
    exit();
}

//Cuando cargue la pagina destruir la session
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    session_destroy();
}

// Create an admin user and password
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123';

// Check if the admin user exists in the database
if (!getUserByEmail($adminEmail)) {
    // Hash the admin password
    $hashedAdminPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Create the admin user in the database
    createUser($adminEmail, $hashedAdminPassword, 'admin');
}

$error = '';

if (isset($_POST['submit']) && !empty($_POST['correu']) && !empty($_POST['pass'])){
    $email = $_POST['correu'];
    $password = $_POST['pass'];

    //Check if the email and password are empty
    if (empty($email) || empty($password)) {
        $error = "Empty email or password";
    }

    //Check if the user exists in the database
    if (!getUserByEmail($email)) {
        $error = "Credencials incorrectes";
    }

    // Retrieve the hashed password from the database
    $hashed_password = getPasswordByEmail($email);

    if ($hashed_password && password_verify($password, $hashed_password)) {
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $email;
        //Comrpovar que el usuario es admin
        if(comprovarAdmin($email)){
            $_SESSION['admin'] = true;
            $_SESSION['prof'] = true;
        }
        if(comprovarProf($email)){
            $_SESSION['prof'] = true;
        }
        if(comprovarAlumno($email)){
            $_SESSION['alumno'] = true;

        }
        if(isset($_SESSION['admin']) && $_SESSION['admin'] === true  || isset($_SESSION['prof']) && $_SESSION['prof'] === true){
            header('Location: home.php');
            exit();
        }else if(isset($_SESSION['alumno']) && $_SESSION['alumno'] === true){
            header('Location: alumnes.activitats.php');
            exit();
        }
    } else {
        $error = "Credencials incorrectes";
    }
}

require '../View/login.vista.php';
?>