<?php 
session_start();
require "function.php";

// COOKIE - mengecek apakah ada cookie atau tidak, jika ada maka set SESSION menjadi true
if( isset($_COOKIE['id'])) {
// var_dump($_COOKIE);die;
    $id = $_COOKIE['id'];
    $cookieDefault = mysqli_query($con, "SELECT * FROM users WHERE id=$id");
    $row = mysqli_fetch_assoc($cookieDefault);

    // mengecek kesamaan username dengan cookie yang sudah di enkripsi
    if( $row['cookie'] === hash("sha256", $row['username'])){
        $_SESSION['loginUser'] = $row['username'];
        $_SESSION['id'] = $row['id'];
    }
}



// Mengecek SESSION sudah terbuat atau belum
if(isset($_SESSION['loginUser'])) {
    header("Location: index.php");
    exit;
}

// Mengecek tombol login
if(isset($_POST['login'])) {

    if( login($_POST) > 0 ) {
        echo "<script>
        document.location.href = 'index.php';
        </script>";
    }else{
        echo mysqli_error($con);
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans&family=Maven+Pro&family=Montserrat&family=Nunito+Sans&family=Quicksand&family=Rubik+Mono+One&display=swap');</style>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
    <nav>
        <h1 style="font-size: 30px;">Savelinks.</h1>
        <span style="background-color: #0093E9; background-image: linear-gradient(160deg, #0093E9 0%, #80D0C7 100%); width: 100%; height: 2px; display: block;"></span>
    </nav>

    <div class="container">
        <main>
                <div class="row">
                    <div class="leftSide" style="text-align:center;">
                        <img src="img/s.png" width="200" style="margin-bottom: 10px;">
                        <h1>Savelinks Project</h1>
                        <p>Website ini bertujuan untuk menyimpan url sehingga dapat memudahkan anda untuk membuka link yang diinginkan tanpa harus membuat url baru</p>
                    </div>

                    <div class="rightSide">
                        <form method="POST" action="" class="loginForm" autocomplete="off">
                            <!-- Menampilkan pesan error -->
                            <?php if(isset($_POST['error'])): ?>
                                <p style="color: red;font-style:italic;font-size:15px;"><?= $_POST['error'] ?></p>
                            <?php endif; ?>

                            <!-- Username -->
                            <div class="login-group username">
                                <label for="username">Username</label><br>
                                <input type="text" name="username" id="username" placeholder="" autofocus>
                            </div>

                            <!-- Password -->
                            <div class="login-group password">
                                <label for="password">Password</label><br>
                                <input type="password" name="password" id="password" placeholder="">
                            </div>
                            <!-- Remember me -->
                            <div class="remember" style="margin: 5px 0;">
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember">Remember me</label>
                            </div>

                            <!-- login -->
                            <div class="login-group login">
                                <button type="submit" class="btn btn-primary" name="login">Login</button>
                            </div>

                        </form>
                        <span class="foot">Belum Punya Akun? <a href="register.php">Regist</a></span>

                    </div>
                </div>
        </main>
    </div>
</body>
</html>