<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $tweetId = $_POST['tweet_id'];
    $comment = $_POST['comment'];

    // Insérer le commentaire dans la base de données
    $sql = "INSERT INTO comments (tweet_id, comment) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Erreur de préparation de la requête SQL : ' . $conn->error);
    }

    $stmt->bind_param("is", $tweetId, $comment);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Le commentaire a été ajouté avec succès.';
    } else {
        echo 'Erreur lors de l\'ajout du commentaire.';
    }

    $stmt->close();
}
?>
