<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $biographie = $_POST['biographie'];

    // Vérifier si l'utilisateur existe déjà dans la base de données
    $sql = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $stmt = mysqli_query($conn, $sql);
    if ($stmt) {
        $row = mysqli_num_rows($stmt);
        if ($row <= 0) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $filename = $_FILES['image']['name'];
            $filetmp = $_FILES['image']['tmp_name'];
            $path = "uploads/" . $filename;
            if (move_uploaded_file($filetmp, $path)) {
                $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, profile, biographie) VALUES ('$name','$email','$hash','$filename','$biographie')";
                $result = mysqli_query($conn, $sql);
                header("location: connexion.php");
            }
        } else {
            session_start();
            $_SESSION['email'] = "Cet email existe déjà";
            header("location: inscription.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter Inscription</title>
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
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"],
        textarea {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #657786;
            width: 250px;
            margin-bottom: 10px;
        }
        
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-twitter"></i> Twitter</h1>
        <p>Avec Twitter, exprimez-vous !<br> Avec les personnes de votre vie.</p>
    </div>

    <?php if (isset($_SESSION['email'])) { ?>
        <p><?php echo $_SESSION['email']; ?></p>
    <?php } ?>

    <header>
        <form method="POST" action="inscription.php" enctype="multipart/form-data">
            <div class="box">
                <input type="text" name="name" id="name" placeholder="Nom complet" required>
                <input type="file" name="image" id="image" required>
                <input type="email" name="email" id="email" placeholder="Adresse e-mail" required>
                <input type="password" name="password" id="password" placeholder="Mot de passe" required>
                <textarea name="biographie" id="biographie" placeholder="Biographie" required></textarea>
            </div>

            <div class="link">
                <input type="submit" value="S'inscrire">
            </div>
            <a href="connexion.php" class="btn btn-success m-2">se connecter j'ai deja un compte</a>
        </form>
    </header>
</body>
</html>
