<?php 
$con = mysqli_connect('localhost', 'root', '', 'todolist') or die("ERROR");

// Fungsi ambil semua data
function query($query) {
    global $con;
    $rows = []; // mendefiniskan array
    $result = mysqli_query($con, $query) or die("GAGAL");
    while( $row = mysqli_fetch_assoc($result)){ // mengeluarkan array
        $rows[] = $row; // mengisi rows dengan array
    }
    return $rows;
}

// --- Register ---
function register($data) { //menangkap value $_POST dengan variabel $data
    global $con;
        // VARIABLE 1 - membersihkan blackslash dan mengubah menjadi huruf kecil semua
        $username = strtolower(stripslashes($data["username"]));
        // VARIABLE 2 - memungkinkan user memasukan password dengan tanda kutip
        $password = mysqli_real_escape_string($con, $data["password"]);
        $confirmPass = mysqli_real_escape_string($con, $data["confirm_password"]);
        $email = mysqli_real_escape_string($con, $data["email"]);
        // VARIABLE 2 - mefilter html
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);
        $confirmPass = htmlspecialchars($confirmPass);
        $email = htmlspecialchars($email);

        // mengecek form kosong atau tidak
        // if( empty(trim($username)) && empty(trim($email)) && empty(trim($password))){
        //     $_POST['error'] = "Username tidak boleh kosong <br> Email tidak boleh kosong <br> Password Tidak boleh kosong";
        //     return false;
        // } else if(empty(trim($username)) && empty(trim($email))){
        //     $_POST['error'] = "Username tidak boleh kosong <br> Email tidak boleh kosong";
        //     return false;
        // } else if( empty(trim($username)) ) {
        //     $_POST['error'] = "Username tidak boleh kosong <br> Email tidak boleh kosong";
        //     return false;
        // }
        // else{
                // KONDISI 1 - Cek username sudah terpakai atau belum
                $result = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
                if(mysqli_fetch_assoc($result)) { // mengeluarkan data dan memberi kondisi 1
                    $_POST['error'] = "username tidak tersedia!";
                    return false; // mengembalikan nilai false kepada else di register php
                }

                // KONDISI 2 - Jika user tersedia maka cek email
                $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
                if(mysqli_fetch_assoc($result)) {
                    $_POST['error'] = "email sudah pernah didaftarkan";
                    return false; // mengembalikan nilai false kepada else di register php
                }

                // KONDISI 2 - Jika KONDISI 1 berhasil maka Lakukan cek konfirmasi password
                if($password !== $confirmPass) {
                    $_POST['errorPass'] = "Konfirmasi Password tidak sesuai";
                    return false;
                }

                // Jika Kondisi 1 & 2 berhasil maka 
                // Enkripsi password lalu menambahkan user
                $password = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($con, "INSERT INTO users SET username='$username', password='$password', email='$email' ");

                // Jika semua query berhasil mengembalikan nilai true
                $_POST['succes'] = "Registrasi berhasil!";
                return mysqli_affected_rows($con);
        // }
}


// --- Login ---
function login($data){
    global $con;

        // VARIABEL 1
        $username = $data['username'];
        $password = $data['password'];

        // Mencegah form kosong
        //if($username!='' && $password != ''){
            // QUERY 1 - Mengecek kesamaan username di database dgn input beserta password
            $query = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($con, $query);

            // KONDISI 1 - Jika username dan password ada di database
            if(mysqli_num_rows($result) === 1 ) {
                // KONDISI 2 - Mengecek Password
                $row = mysqli_fetch_assoc($result);
                if(password_verify($password, $row['password'])){
                    // SESSION - Membuat 1 session dengan key loginUser
                    $_SESSION['loginUser'] = $username;
                    $_SESSION['id'] = $row['id'];

                    // COOKIE - Membuat fitur remember me
                    if(isset($data['remember'])) {
                        // Memasukan cookie ke database
                        $id = $row['id'];
                        $key = hash('sha256', $row['username']); //variabel $key dengan isi username yang diacak
                        mysqli_query($con, "UPDATE users SET cookie='$key' WHERE id=$id");
                        setcookie('id', $id, time()+3600); // mengirim id ke key id
                    }

                    return true;
                } else {
                    $_POST['error'] = "Invalid Username Or Password";
                    return false;
                }
            } else {
                $_POST['error'] = "Invalid Username Or Password";
                return false;
            }
        //} else {
        //    return false;
        //}
}


// --- Tambah Data Tugas ---
function tambahData($data){
    global $con;

    // VARIABEL 1
    $namaTugas = $data['namaTugas'];
    $keterangan = $data['keterangan'];
    $idUser = $_SESSION['id'];

    // KONDISI - Mengecek apakah form kosong
    if($namaTugas != "" && $keterangan != "") {
        // insert data
        $query = "INSERT INTO list_tugas SET id_user=$idUser, nama_tugas='$namaTugas', keterangan_tugas='$keterangan' ";
        $result = mysqli_query($con, $query);
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "Form tidak boleh kosong";
        return false;
    }
}
?>