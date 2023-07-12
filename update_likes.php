<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'identifiant du tweet à partir des données de la requête
    if (isset($_POST['tweet_id'])) {
        $tweetId = $_POST['tweet_id'];

        // Mettre à jour le nombre de likes dans la base de données
        $sql = "UPDATE tweets SET totalLikes = totalLikes + 1 WHERE tweet_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die('Erreur de préparation de la requête SQL : ' . $conn->error);
        }

        $stmt->bind_param("i", $tweetId);
        $stmt->execute();

        // Vérifier si la mise à jour a réussi
        if ($stmt->affected_rows > 0) {
            // Récupérer le nouveau nombre de likes en exécutant une requête distincte
            $sql2 = "SELECT totalLikes FROM tweets WHERE tweet_id = ?";
            $stmt2 = $conn->prepare($sql2);

            if ($stmt2 === false) {
                die('Erreur de préparation de la requête SQL : ' . $conn->error);
            }

            $stmt2->bind_param("i", $tweetId);
            $stmt2->execute();
            $stmt2->bind_result($newLikesCount);

            if ($stmt2->fetch()) {
                // Renvoyer la nouvelle valeur de likes au format texte
                echo $newLikesCount;
            } else {
                echo 'Erreur lors de la récupération du nombre de likes.';
            }
            $stmt2->close();
        } else {
            echo 'Erreur lors de la mise à jour des likes.';
        }
    }
}
?>

