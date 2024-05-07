<section class="vh-100" style="background-color: aliceblue;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-12">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="../../assets/images/login-page.jpg" alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;filter:grayscale(1) blur(1px);" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">

                                <form method="POST" action="../views/login.php">
                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $url = 'http://localhost/api/login/';
                                        $data = [
                                            'email' => $_POST['email'], 'password' => $_POST['password']
                                        ];

                                        $options = [
                                            'http' => [
                                                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                                                'method' => 'POST',
                                                'content' => http_build_query($data),
                                            ],
                                        ];

                                        $context = stream_context_create($options);
                                        $result = file_get_contents($url, false, $context);
                                        if ($result === false) {
                                            echo '<div class="alert alert-danger" role="alert">Erro! tente novamente mais tarde.</div>';
                                        } else {
                                            $result = json_decode($result);
                                            if (isset($result->error)) {
                                                echo '<div class="alert alert-danger" role="alert">' . $result->message . '</div>';
                                            } else {
                                                if (session_status() !== PHP_SESSION_ACTIVE) {
                                                    session_start();
                                                }
                                                $_SESSION["userSessionData"] = json_encode($result);
                                                echo session_status();
                                                header("Location: https://localhost/");
                                                exit;
                                            }
                                        }
                                    }

                                    ?>
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-cubes fa-2x me-3" style="color: #ff621a;"></i>
                                        <span class="h1 fw-bold mb-0">Login</span>
                                    </div>

                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Acesse sua conta</h5>

                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <label class="form-label" for="form2Example17">Email:</label>
                                        <input name="email" type="email" id="form2Example17" class="form-control form-control-lg" required />

                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <label class="form-label" for="form2Example27">Senha:</label>
                                        <input name="password" type="password" id="form2Example27" class="form-control form-control-lg" required />
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button data-mdb-button-init data-mdb-ripple-init name="submit" class="btn btn-dark btn-lg btn-block" type="submit">Entrar</button>
                                    </div>


                                    <p class="mb-5 pb-lg-2" style="color: #393f81;">Não tem conta? <a href="../views/sing-in.php" style="color: #393f81;">Registre-se</a></p>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>