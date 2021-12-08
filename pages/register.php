<?php session_start();


$dsn = "mysql:host=localhost:3306;dbname=website";
$username = 'root';
$password = 'root';

try {
$pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <title>Login</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                < To-Do's />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="#">Home</a>
                    <a class="nav-link" href="#">Lists</a>
                    <a class="nav-link" href="./login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <p class="mt-4 text-center"><?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['log_username'];
            $email = $_POST['log_email'];
            $password = $_POST['log_password'];
            $password_conf = $_POST['log_password_conf'];
            $admin = 0;


            if ($password !== $password_conf) {
                echo "Il faut que les deux mot de passe soient les mêmes.";
            }

            $cryptedPswd = password_hash($password, PASSWORD_BCRYPT);
            echo $cryptedPswd;
            
            $query = "INSERT INTO users (username, email, password, admin) VALUES (:username, :email, :password, :admin);";
            $datas = [
                "username" => $username,
                "email" => $email,
                "password" => $cryptedPswd,
                "admin" => $admin
            ];

            $result = $pdo->prepare($query);
            $result->execute($datas);
        }
    ?></p>
    <div class="container text-center">
        <div class="form-signin">
            <form class="mt-5 needs-validation" method="POST" novalidate>
                <label for="validationCustomUsername" class="form-label">Veuillez vous connecter</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="text" class="form-control" name="log_username" id="validationCustomUsername"
                        aria-describedby="inputGroupPrepend" required>
                    <div class="invalid-feedback">
                        Please choose a username.
                    </div>
                </div>
                <div class="form-floating mt-2">
                    <input type="email" class="form-control" name="log_email"
                        pattern="[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?"
                        id="validationCustomEmail" placeholder="E-Mail" required>
                    <label for="floatingPassword">E-Mail</label>
                    <div class="invalid-feedback">
                        E-Mail invalide.
                    </div>
                </div>
                <div class="form-floating mt-2">
                    <input type="password" class="form-control" name="log_password"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*_=+-.]).{8,12}$"
                        id="validationCustomEmail" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <div class="invalid-feedback">
                        Le mot de passe doit avoir au moins 8 à 12 caractères de long, 1 majuscule, 1 minuscule, 1
                        chiffre et un caractère spécial.
                    </div>
                </div>
                <div class="form-floating mt-2">
                    <input type="password" name="log_password_conf" class="form-control" id="validationCustom02"
                        placeholder="Password" required>
                    <label for="floatingPassword">Confirm Password</label>
                </div>
                <div class="w-100">
                    <button class="btn btn-primary" type="submit">Connect</button>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation:not(.passwd)')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>