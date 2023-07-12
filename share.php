<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'identifiant du tweet à partager
    $tweetId = $_POST['tweet_id'];

    // Mettre à jour le nombre de partages dans la base de données
    $sql = "UPDATE tweets SET shares = shares + 1 WHERE tweet_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Erreur de préparation de la requête SQL : ' . $conn->error);
    }

    $stmt->bind_param("i", $tweetId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Le tweet a été partagé avec succès.';
    } else {
        echo 'Erreur lors du partage du tweet.';
    }

    $stmt->close();
}
?>
