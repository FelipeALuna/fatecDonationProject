<section class="vh-100" style="background-color: aliceblue;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-12">
                <div class="card" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-5 col-lg-5 d-none d-md-block">
                            <img src="../../assets/images/girl-2529908_1920.jpg" alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;width:100%;height: 100%;filter:grayscale(1) blur(1px);"/>
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">

                                <form method="POST" action="../views/sing-in.php">
                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $url = 'http://localhost/api/register/';
                                        $data = [
                                            'email' => $_POST['email'], 'password' => $_POST['password'],
                                            'phone' => $_POST['phone'], 'adress' => $_POST['adress'],
                                            'name'=>$_POST['name']

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
                                            if ($result->error == true) {
                                                echo '<div class="alert alert-danger" role="alert">' . $result->message . '</div>';
                                            } else {
                                                echo '<div class="alert alert alert-success" role="alert"> Usuário criado com Sucesso!</div>';
                                                header("Location: https://localhost/");
                                                exit;
                                            }
                                        }
                                    }

                                    ?>
                                    
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-cubes fa-2x me-3" style="color: #ff621a;"></i>
                                        <span class="h1 fw-bold mb-0">Registrar-se</span>
                                    </div>

                                    <h5 class="fw-normal mb-4 pb-1" style="letter-spacing: 1px;">Digite seus dados</h5>
                                    <div data-mdb-input-init class="form-outline mb-1">
                                        <label class="form-label" for="name">Nome:</label>
                                        <input name="name" type="text" id="name" class="form-control form-control-lg" required />

                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-1">
                                        <label class="form-label" for="email">Email:</label>
                                        <input name="email" type="email" id="email" class="form-control form-control-lg" required />

                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-1">
                                        <label class="form-label" for="adress">Endereço:</label>
                                        <input name="adress" type="text" id="adress" class="form-control form-control-lg" required />

                                    </div>
                                    <div data-mdb-input-init class="form-outline mb-1">
                                        <label class="form-label" for="phone">Telefone:</label>
                                        <input name="phone" type="tel" id="phone" class="form-control form-control-lg" required />

                                    </div>

                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <label class="form-label" for="password">Senha:</label>
                                        <input name="password" type="password" id="password" class="form-control form-control-lg" required />
                                    </div>

                                    <div class="pt-1 mb-4">
                                        <button data-mdb-button-init data-mdb-ripple-init name="submit" class="btn btn-dark btn-lg btn-block" type="submit">Registrar-se</button>
                                    </div>


                                    <p class="mb-5 pb-lg-2" style="color: #393f81;">Já tem conta? <a href="../views/login.php" style="color: #393f81;">Realizar Login</a></p>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>