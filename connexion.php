<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Échapper les valeurs pour éviter les injections SQL (optionnel, mais recommandé)
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Requête pour vérifier les informations d'identification de l'utilisateur
    $sql = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn,$sql);

    // Vérifier si la requête a renvoyé un résultat
    if ($result) {
     $row=mysqli_fetch_assoc($result);
     if ($row) {
         if (password_verify($password, $row["mot_de_passe"])) {
             session_start();
             $_SESSION['id']=$row['id'];
             $_SESSION['email']=$row['email'];
             header("location: index.php");
         }
         else{
            session_start();
            $_SESSION['$erreur']= "email ou mot de passe incorrect";
            header("location: connexion.php");
        }
        
    } 

}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter - Login</title>
    <link rel="stylesheet" href="./bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./icons-1.9.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #15202b;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .container {
            text-align: center;
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        p {
            font-size: 18px;
            margin-bottom: 20px;
        }
        
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .box {
            margin-bottom: 20px;
        }
        
        input[type="email"],
        input[type="password"] {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #657786;
            width: 250px;
        }
        
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .link a {
            color: #1da1f2;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="logo.jpg" alt="Twitter Icon" width="64" height="64">
        <h1>Twitter</h1>
        <p>Twitter helps you connect and share<br> with the people in your life.</p>
        <form method="POST" action="connexion.php">
            <div class="box">
                <section>
                    <input type="email" name="email" id="email" placeholder="Email address or phone number">
                </section>
                <section>
                    <input type="password" name="password" id="password" placeholder="Password">
                </section>
            </div>

            <div class="link">
                <input type="submit" value="Log in">
            </div>

            <div class="link1">
                <a href="#">Forgot password?</a>
            </div>

            <div class="link2">
                <a href="inscription.php">Sign up for a new account</a>
            </div>
        </form>
    </div>
</body>
</html>
