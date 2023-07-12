<?php
include('db.php');
if (isset($_POST['tweet_id'])) {
    $tweetId = $_POST['tweet_id'];
    $sql = "UPDATE tweets SET retweets = retweets + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tweetId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo 'Le nombre de retweets a été incrémenté avec succès.';
    } else {
        echo 'Erreur lors de l\'incrémentation du nombre de retweets.';
    }
    $conn->close();
}
?>
