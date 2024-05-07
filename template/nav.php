<?php

require($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$userAuthenticade = false;
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (isset($_SESSION['userSessionData'])) {
    $userAuthenticade = true;
    $userData = json_decode($_SESSION['userSessionData']);

    $authKey = new AuthKey();
    $authKey = $authKey->getAuthKey();
    $decoded = JWT::decode($userData->token, new Key($authKey, 'HS256'));
    $userName = $decoded->name;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: https://localhost/index.php");
    exit;
}

?>

<nav class="navbar navbar-expand-lg navbar navbar-light bg-gradient ">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarTogglerDemo01">
            <a class="navbar-brand" href="/"><img src="../assets/images/ong-icon.png" style="max-width:100px;"></img></a>
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0 ">
                <li class="nav-item">
                    <a class="nav-link" href="/">Página Inicial</span></a>
                </li>
                <?php if (!$userAuthenticade) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../views/sing-in.php">Registrar-se</a>
                    </li>
                <?php endif; ?>

            </ul>
            <?php if ($userAuthenticade)
                echo '<div class="form-inline pb-1">
                <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
              </svg>
            <div>Olá, ' . $userName . '</div>
            <a href="/?logout" class="mx-4 my-2 text-dark" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
          </svg>
          </a>
           
      
        </form>'
            ?>

        </div>
    </div>
</nav>