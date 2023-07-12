<?php
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validation des données (vous pouvez ajouter des validations supplémentaires selon vos besoins)
    if (empty($email) || empty($password)) {
        // Les champs requis ne sont pas remplis, afficher un message d'erreur
        echo "Veuillez remplir tous les champs requis.";
    } else {
        // Échapper les valeurs pour éviter les injections SQL (utilisation de mysqli_real_escape_string)
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        // Vérifier si l'utilisateur existe dans la base de données
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];

            // Vérifier le mot de passe
            if (password_verify($password, $hashedPassword)) {
                // Le mot de passe est correct, connecter l'utilisateur
                
                // Vous pouvez effectuer des actions supplémentaires ici, comme définir les sessions de l'utilisateur, etc.
                
                // Rediriger l'utilisateur vers la page d'accueil
                header("Location: index.php");
                exit();
            } else {
                // Le mot de passe est incorrect, afficher un message d'erreur
                echo "Mot de passe incorrect.";
                header("Location: connexion.php");
            }
        } else {
            // L'utilisateur n'existe pas, afficher un message d'erreur
            echo "Adresse e-mail incorrecte.";
        }
    }
}
?>
