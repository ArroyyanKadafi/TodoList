<?php 
session_start();
require "function.php";

// Mengecek session user
if( !$_SESSION['loginUser'] && !$_SESSION['id'] ){
    header("Location: login.php");
}

if( isset($_POST['kirim']) ){
    // Jalankan function
    if( tambahData($_POST) > 0){
        $_POST['succes'] = "Data berhasil ditambah";
        // Do nothing
    } else {
        echo mysqli_error($con);
    }
}

$id = $_SESSION['id'];
$list_tugas = query("SELECT * FROM list_tugas WHERE id_user=$id");


// --- Logout ---
if( isset($_GET['logout'])=='true') {
    session_destroy();
    $_SESSION = [];
    header("Location: login.php");
    // COOKIE logout
    $id = $_COOKIE['id']; // mengambil id
    mysqli_query($con, "UPDATE users SET cookie='' WHERE id=$id"); // mengubah cookie di database menjadi kosong
    setcookie('id', '', time()-3600);
}


// --- Delete ---
if( isset($_GET['delete'])=='true'){

    // Clear All
    if(isset($_GET['idus'])){
        $idUser = $_GET['idus'];
        var_dump($idUser);
        if($result = mysqli_query($con, "DELETE FROM list_tugas WHERE id_user=$idUser")){
           header("Location: index.php");
        } else {
            header("Location: index.php?error");
        }

    } else if(isset($_GET['idlist'])){
        // Delete by 1
        $idTugas = $_GET['idlist'];
        var_dump($idTugas);
        if( $result = mysqli_query($con, "DELETE FROM list_tugas WHERE id_tugas=$idTugas")){
            header("Location: index.php");
        } else {
            header("Location: index.php?error");
        }
    }
}
?>

<html lang="id">
<head>
    <title>Pas pwpb - Buku Tamu</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <header>
            <h1>To-Do List</h1>
            <hr>
        </header>

        <div class="formTodo">
            <form method="POST" action="" class="loginForm" autocomplete="off">
                <!-- Menampilkan pesan error dari function -->
                <?php if(isset($_POST['error'])): ?>
                    <p style="color: red;font-style:italic;font-size:15px;"><?= $_POST['error'] ?></p>
                <?php endif; ?>

                <!-- Menampilkan pesan error dari url -->
                <?php if(isset($_GET['error'])): ?>
                    <p style="color: red;font-style:italic;font-size:15px;"><?= "gagal menghapus data" ?></p>
                <?php endif; ?>

                <!-- Menampilkan pesan succes -->
                <?php if(isset($_POST['succes'])): ?>
                    <p style="color: green;font-style:italic;font-size:15px;"><?= $_POST['succes'] ?></p>
                <?php endif; ?>

                <!-- Nama tugas -->
                <div class="login-group namaTugas">
                    <label for="namaTugas">Nama tugas</label><br>
                    <input type="text" name="namaTugas" id="namaTugas" placeholder="tambahkan tugas baru">
                </div>

                <!-- keterangan -->
                <div class="login-group keterangan">
                    <label for="keterangan">Keterangan</label><br>
                    <input type="keterangan" name="keterangan" id="keterangan" placeholder="deskripsi tugas">
                </div>

                <!-- Kirim -->
                <div class="login-group kirim" style="margin-top: 20px;">
                    <button type="submit" name="kirim" class="btn btn-primary"><i class="fas fa-plus" style="font-size: 20px; color:white;"></i></button>
                </div>

            </form>

            <a href="index.php?logout=true" class="btn btn-danger" text-decoration:none; padding:5px;">Log Out</a>

            <a href="index.php?delete=true&idus=<?= $list_tugas[0]['id_user'] ?>" class="clearAll btn btn-success"  onclick="return confirm('Apa anda yakin ingin menghapus semua tugas?')">Clear All</a>
        </div>

        <!-- Start List -->
        <div class="listTodo">
            <ol class="list-num">
                <!-- menampilkan list tugas -->
                <?php foreach($list_tugas as $tugas): ?>
                    <div class="flexing">
                        <div class="theleft">
                            <div class="listGroup">
                                <li style="font-size: 18px;"><?= $tugas['nama_tugas'] ?></li>
                                <ul>
                                    <li><?= $tugas['keterangan_tugas'] ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="break">
                            <a href="index.php?delete=true&idlist=<?=$tugas['id_tugas']?>"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ol>
        </div>

    </div>
</body>
</html>