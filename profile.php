<?php
session_start();
require_once('db.php');
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}

$userID = $_SESSION['id'];

// Requête SQL pour récupérer les informations de profil de l'utilisateur
$query = "SELECT * FROM utilisateurs WHERE id = $userID";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "<p class='text-light'>Aucun utilisateur trouvé.</p>";
}

// Requête SQL pour récupérer les publications de l'utilisateur
$query = "SELECT * FROM tweets WHERE auteur_tweets = $userID ORDER BY created_at DESC";
$tweetsResult = mysqli_query($conn, $query);

$tweets = array();
if (mysqli_num_rows($tweetsResult) > 0) {
    while ($tweet = mysqli_fetch_assoc($tweetsResult)) {
        $tweets[] = $tweet;
    }
}

// Modifier la photo de profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photo'])) {
    // Vérifier si un fichier a été téléchargé
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        // Définir les extensions de fichiers autorisées
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        // Obtenir les informations sur le fichier téléchargé
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $extension = strtolower($fileInfo['extension']);

        // Vérifier l'extension du fichier
        if (in_array($extension, $allowedExtensions)) {
            // Déplacer le fichier vers le répertoire des téléchargements
            $newFilename = 'profile_' . $userID . '.' . $extension;
            $destination = 'uploads/' . $newFilename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                // Mettre à jour le chemin de la photo de profil dans la base de données
                $updateQuery = "UPDATE utilisateurs SET profile = '$newFilename' WHERE id = $userID";
                if (mysqli_query($conn, $updateQuery)) {
                    // Rediriger vers la page de profil mise à jour
                    header('Location: profile.php');
                    exit();
                } else {
                    echo "Erreur lors de la mise à jour de la photo de profil.";
                }
            } else {
                echo "Erreur lors du téléchargement de la photo de profil.";
            }
        } else {
            echo "Extension de fichier non autorisée. Veuillez choisir une image au format JPG, JPEG ou PNG.";
        }
    }
}

// Modifier la biographie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_biography'])) {
    $newBiography = $_POST['biography'];

    // Mettre à jour la biographie dans la base de données
    $updateQuery = "UPDATE utilisateurs SET biographie = '$newBiography' WHERE id = $userID";
    if (mysqli_query($conn, $updateQuery)) {
        // Rediriger vers la page de profil mise à jour
        header('Location: profile.php');
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la biographie.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_photo'])) {
    $filename = 'uploads/' . $user['profile'];
    if (unlink($filename)) {
        // Mettre à jour le chemin de la photo de profil dans la base de données
        $updateQuery = "UPDATE utilisateurs SET profile = NULL WHERE id = $userID";
        if (mysqli_query($conn, $updateQuery)) {
            // Rediriger vers la page de profil mise à jour
            header('Location: profile.php');
            exit();
        } else {
            echo "Erreur lors de la suppression de la photo de profil.";
        }
    } else {
        echo "Erreur lors de la suppression de la photo de profil.";
    }
}
// Récupérer l'identifiant de l'utilisateur connecté
$user_id = 1; // Remplacez par la logique appropriée pour obtenir l'ID de l'utilisateur connecté

// Récupérer les identifiants des amis de l'utilisateur connecté à partir de la table 'friends'
$sql = "SELECT friend_id FROM friends WHERE user_id = $user_id";
// Exécuter la requête SQL et récupérer les résultats

// Construire la liste des identifiants d'amis
$friendIds = [];
while ($row = mysqli_fetch_assoc($result)) {
    $friendIds[] = $row['friend_id'];
}

// Récupérer les publications associées aux amis de l'utilisateur connecté à partir de la table 'posts'
$friendIdsStr = implode(',', $friendIds);
$sql = "SELECT * FROM posts WHERE user_id IN ($friendIdsStr)";
// Exécuter la requête SQL et récupérer les résultats

// Afficher les publications
while ($row = mysqli_fetch_assoc($result)) {
    // Afficher les détails de la publication
    echo "Publication : " . $row['content'] . "<br>";
}



mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="style.css">
    <!-- Lightbox for Gallery -->
    <link rel="stylesheet" href="lightbox.min.css">
    <script type="text/javascript" src="lightbox-plus-jquery.min.js"></script>
    <!-- Lightbox for Gallery -->

    <title>Profil</title>
</head>
<style>
    .logo {
        display: flex;
        align-items: center;
        font-size: 24px;
        font-weight: bold;
    }

    .logo img {
        height: 70px;
        width: auto;
        margin-right: 10px;
    }
</style>
<body>
    <!-- Navigation Starts -->
    <nav class="navbar navbar-expand-md navbar-dark" style="background-color:#3097D1">
        <div class="logo">
            <img src="logo.jpg" alt="twriter" style="height: 70px; width: 100%;" class="rounded">
            <a href="#" class="navbar-brand" title="Afficher la page d'accueil">
                Twritter
            </a>
        </div>
        <button class="navbar-toggler" data-toggle="collapse" data-target="#responsive"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="responsive">
            <ul class="navbar-nav mr-auto text-capitalize">
                <li class="nav-item"><a href="index.php" class="nav-link active">home</a></li>
                <li class="nav-item"><a href="profile.php" class="nav-link">profile</a></li>
                <li class="nav-item"><a href="#modalview" data-toggle="modal" class="nav-link">messages</a></li>
                <li class="nav-item"><a href="notification.html" class="nav-link">docs</a></li>
                <li class="nav-item"><a href="#" class="nav-link d-md-none">growl</a></li>
                <li class="nav-item"><a href="#" class="nav-link d-md-none">logout</a></li>
            </ul>
            <form action="" class="form-inline ml-auto d-none d-md-block">
                <input type="text" name="search" id="search" placeholder="Search" class="form-control form-control-sm">
            </form>
            <?php if (isset($user)) : ?>
                <img src="uploads/<?php echo $user['profile']; ?>" alt="Photo de profil" class="rounded-circle ml-3 d-none d-md-block" width="32px" height="32px">
                <span class="online-indicator"></span>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $user['nom']; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user-circle"></i> Profil
                        </a>
                        <a class="dropdown-item" href="notification.php">
                            <i class="fas fa-envelope"></i> Messages
                        </a>
                        <a class="dropdown-item" href="notification.html">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="connexion.php">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <!-- End of Navigation -->

    <!-- Modal Section -->
    <div class="modal fade" id="modalview">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title h4">Messages</div>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <ul class="list-unstyled">
                        <a href="#" class="text-decoration-none">
                            <li class="media hover-media">
                                <img src="img/avatar-dhg.png" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                                <div class="media-body text-dark">
                                    <h6 class="media-header">Jchob Thunder and <strong> 1 others</strong></h6>
                                    <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                </div>
                            </li>
                        </a>
                        <hr class="my-3">
                        <!-- Other message items... -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Modal Section -->

    <!-- Banner/Image Section -->
    <div class="banner">
        <div class="banner-title d-flex flex-column justify-content-center align-items-center">
            <img src="uploads/<?php echo $user['profile']; ?>" alt="img" class="rounded-circle" width="80px" height="80px">
            <h3 class="text-light"><?php echo $user['nom']; ?></h3>
            <p class="text-light"><?php echo $user['biographie']; ?></p>
        </div>
        <div class="banner-end d-flex justify-content-center align-items-end">
            <ul class="nav text-light">
                <li class="nav-item nav-link active">Photos</li>
                <li class="nav-item nav-link">Others</li>
                <li class="nav-item nav-link">Anothers</li>
            </ul>
        </div>
    </div>
    <!-- End of Banner/Image Section -->

    <!-- Image Portfolio -->
    <div class="grid-template container my-4">
        <div class="item-1">
            <a href="portfolio/img1.jpg" data-lightbox="id"><img src="portfolio/img1.jpg" alt="" class="img-fluid" style="width:455px; height: 255px;"></a>
        </div>
        <!-- Other image items... -->
    </div>

    <!-- Modification de la photo de profil -->
    <div class="container">
        <h4>Modifier la photo de profil</h4>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="photo" class="form-control-file">
            </div>
            <button type="submit" name="update_photo" class="btn btn-primary">Enregistrer</button>
            <button type="submit" name="delete_photo" class="btn btn-danger">Supprimer</button>
        </form>
    </div>

    <!-- Modification de la biographie -->
    <div class="container">
        <h4>Modifier la biographie</h4>
        <form method="POST">
            <div class="form-group">
                <textarea name="biography" rows="3" class="form-control"><?php echo $user['biographie']; ?></textarea>
            </div>
            <button type="submit" name="update_biography" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>