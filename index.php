<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php');
    exit();
}
require_once('db.php');
if (isset($row)) {
    $_SESSION['id'] = $row['id'];
    $_SESSION['email'] = $row['email'];
    header("Location: index.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $content = $_POST['content'];
        $image = '';
        if ($_FILES['photo']['name']) {
            $targetDirectory = 'img/';
            $fileName = $_FILES['photo']['name'];
            $targetPath = $targetDirectory . $fileName;
            move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);

            $image = $targetPath;
        }
        $query = "INSERT INTO tweets (image, content, created_at, auteur_tweets) VALUES ('$image', '$content', NOW(), '$userId')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            header('Location: index.php');
            exit();
        } else {
            $error = "Une erreur s'est produite lors de la publication du tweet.";
        }
    } else {
        header('Location: connexion.php');
        exit();
    }
}
$query = "SELECT * FROM tweets ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$tweets = mysqli_fetch_all($result, MYSQLI_ASSOC);
foreach ($tweets as &$tweet) {
    $userId = $tweet['auteur_tweets'];
    $userQuery = "SELECT * FROM utilisateurs WHERE id = '$userId'";
    $userResult = mysqli_query($conn, $userQuery);
    $user = mysqli_fetch_assoc($userResult);
    $tweet['user'] = $user;
}
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $userQuery = "SELECT * FROM utilisateurs WHERE id = '$userId'";
    $userResult = mysqli_query($conn, $userQuery);
    $user = mysqli_fetch_assoc($userResult);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tweet_id'])) {
        $tweetID = $_POST['tweet_id'];

        if (isset($_POST['like'])) {
            $query = "UPDATE tweets SET totalLikes = totalLikes + 1 WHERE id = $tweetID";
            mysqli_query($conn, $query);
            $query = "UPDATE utilisateurs SET totalLikes = totalLikes + 1 WHERE id = $userID";
            mysqli_query($conn, $query);
        }
        if (isset($_POST['comment'])) {
            $comment = $_POST['comment'];
            $query = "INSERT INTO comments (tweet_id, user_id, comment) VALUES ($tweetID, $userID, '$comment')";
            mysqli_query($conn, $query);
            $query = "UPDATE tweets SET totalComments = totalComments + 1 WHERE id = $tweetID";
            mysqli_query($conn, $query);
            $query = "UPDATE utilisateurs SET totalComments = totalComments + 1 WHERE id = $userID";
            mysqli_query($conn, $query);
        }
        if (isset($_POST['share'])) {
            if (isset($_POST['confirm_share'])) {
                $query = "UPDATE tweets SET totalShares = totalShares + 1 WHERE id = $tweetID";
                mysqli_query($conn, $query);
                $query = "UPDATE utilisateurs SET totalShares = totalShares + 1 WHERE id = $userID";
                mysqli_query($conn, $query);
            }
        }
    }
}
if (isset($_POST['tweet_id'], $_POST['content'])) {
    $tweetId = $_POST['tweet_id'];
    $content = $_POST['content'];


    header('Location: profile.php');
    exit();
}
if (isset($_POST['tweet_id'], $_POST['delete'])) {
    $tweetId = $_POST['tweet_id'];
    header('Location: profile.php');
    exit();
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<style>
     body {
      background-color: #f8f9fa;
      color: #333;
    }

    header {
      background-color: #007bff;
      padding: 10px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

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
    .online-indicator {
      display: inline-block;
      width: 10px;
      height: 10px;
      background-color: green;
      border-radius: 50%;
      margin-left: 5px;
    }

</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="./bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./icons-1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <!------------------LIght BOx for Gallery-------------->
    <link rel="stylesheet" href="lightbox.min.css">
    <script type="text/javascript" src="lightbox-plus-jquery.min.js"></script>
    <!------------------LIght BOx for Gallery-------------->
    <title>Application-1</title>
</head>
<body>


    <!-------------------------------NAvigation Starts------------------>

    <nav class="navbar navbar-expand-md navbar-dark mb-4" style="background-color:#3097D1">
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
                <li class="nav-item"><a href="#modalview" class="nav-link" data-toggle="modal">messages</a></li>
                <li class="nav-item"><a href="notification.php" class="nav-link">docs</a></li>
                <li class="nav-item"><a href="#" class="nav-link d-md-none">growl</a></li>
                <li class="nav-item"><a href="#" class="nav-link d-md-none">logout</a></li>

            </ul>

            <form action="" class="form-inline ml-auto d-none d-md-block">
                <input type="text" name="search" id="search" placeholder="Search" class="form-control form-control-sm">
            </form>
            <div class="profile-menu">
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
                                    <a class="dropdown-item" href="messages.php">
                                        <i class="fas fa-envelope"></i> Messages
                                    </a>
                                    <a class="dropdown-item" href="notification.php">
                                        <i class="fas fa-bell"></i> Notifications
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="deconnex.php">
                                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                                    </a>
                                </div>
                            </div>
                <?php endif; ?>
            </div>      
    </nav>
    <div class="text-center">
    <h1>Bienvenue, <?php echo $user['nom']; ?></h1>
    <img src="uploads/<?php echo $user['profile']; ?>" alt="Photo de profil" class="img-circle rounded mr-2 align-center">
    <!-- Autres informations de l'utilisateur -->
</div>

    <!---------------------------------------------Ends navigation------------------------------>

    <!---------------------------MOdal Section  satrts------------------->

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

                            <img src="img/Screenshot_20230626-195031_Photos.jpg" alt="img" width="60px" height="60px" class="rounded-circle mr-3">

                            <div class="media-body text-dark">
                                <h6 class="media-header">Jchob Thunder and <strong> 1 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>

                            </div>

                        </li>
                    </a>
                    <hr class="my-3">


                    
                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-fat.jpg" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Mark Otto and <strong> 3 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>


                    <hr class="my-3">


                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-mdo.png" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Archer andu and <strong> 5 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>

                    <hr class="my-3">


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
                    
                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-fat.jpg" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Mark Otto and <strong> 3 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>
                    
                    
                    <hr class="my-3">
                    
                    
                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-mdo.png" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Archer andu and <strong> 5 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>

                    
                    <hr class="my-3">
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
                    
                    
                    
                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-fat.jpg" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Mark Otto and <strong> 3 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>
                    
                    
                    <hr class="my-3">
                    
                    
                    <a href="#" class="text-decoration-none">
                        <li class="media hover-media">

                            <img src="img/avatar-mdo.png" alt="img" width="60px" height="60px" class="rounded-circle mr-3">
                            
                            <div class="media-body text-dark">
                                <h6 class="media-header">Archer andu and <strong> 5 others</strong></h6>
                                <p class="media-text">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                
                            </div>
                            
                        </li>
                    </a>
                </ul>
            </div>
        </div>


    </div>


</div>

<!-------------------------------MOdal Ends---------------------------->
<!-------------------------------------------Start Grids layout for lg-xl-3 columns and (xs-lg 1 columns)--------------------------------->


<div class="container">
    <div class="row">
        <!--------------------------left columns  start-->

        <div class="col-12 col-lg-3">

            <div class="left-column">
            <div class="card card-left1 mb-4">
            <div class="card-body text-center">
       <a href="profile.php"><img src="uploads/<?php echo $user['profile']; ?>" alt="img" width="120px" height="120px" class="rounded-circle mt-n5">
                        </a><h5 class="card-title"><?php echo $user['nom']; ?></h5>
        <p class="card-text text-justify mb-2"><?php echo $user['biographie']; ?></p>
        <ul class="list-unstyled nav justify-content-center">
            <li class="nav-item">Friends<br><strong><?php echo $user['nb_amis']; ?></strong></li>
            <li class="nav-item">Enimes<br><strong><?php echo $user['nb_ennemis']; ?></strong></li>
        </ul>
    </div>
</div>



             <div class="card shadow-sm card-left2 mb-4">

                <div class="card-body">

                    <h5 class="mb-3 card-title">About <small><a href="#" class="ml-1">Edit</a></small></h5>

                    <p class="card-text"> <i class="fas fa-calendar-week mr-2"></i> Went to <a href="#" class="text-decoration-none">oh canada</a></p>

                    <p class="card-text"> <i class="fas fa-user-friends mr-2"></i> Become a friend with <a href="#" class="text-decoration-none">obama</a></p>
                    <p class="card-text"> <i class="far fa-building mr-2"></i> Work at <a href="#" class="text-decoration-none">Github</a></p>
                    <p class="card-text"> <i class="fas fa-home mr-2"></i> Live in <a href="#" class="text-decoration-none">San francisco</a></p>
                    <p class="card-text"> <i class="fas fa-map-marker mr-2"></i> From <a href="#" class="text-decoration-none">Seattle, WA</a></p>
                </div>
            </div>
            <div class="card shadow-sm card-left3 mb-4">

                <div class="card-body">
                    <h5 class="card-title">Photos<small class="ml-2"><a href="#">.Edit </a></small></h5>

                    <div class="row">
                        <div class="col-6 p-1">
                            <a href="img/left1.jpg" data-lightbox="id" ><img src="img/left1.jpg" alt="img" class="img-fluid my-2"></a>  
                            <a href="img/left2.jpg"data-lightbox="id"><img src="img/left2.jpg" alt="img" class="img-fluid my-2"></a>
                            <a href="img/left3.jpg"data-lightbox="id"><img src="img/left3.jpg" alt="img" class="img-fluid my-2"></a>

                        </div>


                        <div class="col-6 p-1">
                            <a href="img/left4.jpg"data-lightbox="id"><img src="img/left4.jpg" alt="img" class="img-fluid my-2"></a>
                            <a href="img/left5.jpg"data-lightbox="id"><img src="img/left5.jpg" alt="img" class="img-fluid my-2"></a>
                            <a href="img/left6.jpg"data-lightbox="id"><img src="img/left6.jpg" alt="img" class="img-fluid my-2"></a>
                            
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    <!--------------------------Ends Left columns-->


    <!---------------------------------------Middle columns  start---------------->
    <div class="col-12 col-lg-6">
    

<!-- Affichage des tweets dans la section appropriée de la page -->
<div class="middle-column">
    <div class="card">
        <div class="card-header bg-transparent">
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <div class="input-group w-100">
                    <input type="text" name="content" placeholder="Message" class="form-control form-control-md">

                    <div class="input-group-append">
                        <label for="photo-upload" class="input-group-text">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                </div>

                <div class="mt-3">
                    <input type="file" name="photo" id="photo-upload" class="d-none">
                    <img id="selected-photo" src="#" alt="Selected Photo" style="max-width: 100%; height: auto; display: none;">
                </div>

                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-primary">Publier</button>
                </div>
            </form>
        </div>
        <div class="card-body">
    <?php foreach ($tweets as $tweet) : ?>
        <div class="media">
            <img src="uploads/<?php echo isset($tweet['user']['profile']) ? $tweet['user']['profile'] : ''; ?>" alt="" width="55px" height="55px" class="rounded-circle mr-3">

            <div class="media-body">
                <?php if (isset($tweet['user']) && isset($tweet['user']['nom'])) : ?>
                    <h5><?php echo $tweet['user']['nom']; ?></h5>
                <?php endif; ?>
                <p class="card-text text-justify"><?php echo $tweet['content']; ?></p>

                <?php if (!empty($tweet['image'])) : ?>
                    <img src="<?php echo $tweet['image']; ?>" alt="Tweet Image" class="img-fluid">
                <?php endif; ?>

                <div class="interaction-icons">
                    <form class="like-form" data-tweet-id="<?php echo $tweet['id']; ?>" action="update_likses.php">
                        <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                        <button type="button" class="icon-btn like-btn <?php echo $tweet['isLiked'] ? 'liked' : ''; ?>">
                            <i class="bi bi-heart"></i> <span class="like-count"><?php echo isset($tweet['totalLikes']) ? $tweet['totalLikes'] : 0; ?></span>
                        </button>
                    </form>

                    <button type="button" class="icon-btn comment-btn" data-toggle="modal" data-target="#commentModal<?php echo $tweet['id']; ?>">
                        <i class="bi bi-chat"></i> <?php echo isset($tweet['totalComments']) ? $tweet['totalComments'] : 0; ?>
                    </button>

                    <button type="button" class="icon-btn share-btn" data-toggle="modal" data-target="#shareModal<?php echo $tweet['id']; ?>">
                        <i class="bi bi-arrow-counterclockwise"></i> <?php echo isset($tweet['totalShares']) ? $tweet['totalShares'] : 0; ?>
                    </button>

                    <button type="button" class="icon-btn edit-btn" data-toggle="modal" data-target="#editModal<?php echo $tweet['id']; ?>">
                        <i class="bi bi-pencil"></i> Modifier
                    </button>

                    <button type="button" class="icon-btn delete-btn" data-toggle="modal" data-target="#deleteModal<?php echo $tweet['id']; ?>">
                        <i class="bi bi-trash"></i> Supprimer
                    </button>
                </div>
            </div>
            <small><?php echo $tweet['created_at']; ?></small>
        </div>
        <hr>
    <?php endforeach; ?>
</div>

<!-- Modal de Commentaire -->
<?php foreach ($tweets as $tweet) : ?>
    <div class="modal fade" id="commentModal<?php echo $tweet['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Commenter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire de commentaire -->
                    <form method="POST" action="">
                        <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Votre commentaire"></textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" form="commentForm<?php echo $tweet['id']; ?>" class="btn btn-primary">Envoyer</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal de Partage -->
<?php foreach ($tweets as $tweet) : ?>
    <div class="modal fade" id="shareModal<?php echo $tweet['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">Partager</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Voulez-vous partager ce tweet ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
                    <form method="POST" action="">
                        <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                        <input type="hidden" name="confirm_share" value="true">
                        <button type="submit" name="share" class="btn btn-primary">Oui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal de Modification -->
<?php foreach ($tweets as $tweet) : ?>
    <div class="modal fade" id="editModal<?php echo $tweet['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier la publication</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" action="">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire de modification -->
                    <form method="POST" action="">
                        <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                        <textarea name="content" class="form-control" rows="3"><?php echo $tweet['content']; ?></textarea>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" form="editForm<?php echo $tweet['id']; ?>" class="btn btn-primary">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal de Suppression -->
<?php foreach ($tweets as $tweet) : ?>
    <div class="modal fade" id="deleteModal<?php echo $tweet['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Supprimer la publication</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Voulez-vous vraiment supprimer cette publication ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Non</button>
                    <form method="POST" action="">
                        <input type="hidden" name="tweet_id" value="<?php echo $tweet['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger">Oui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


                <div class="card-body">
                    <div class="media">
                    <img src="uploads/<?php echo isset($tweet['user']['profile']) ? $tweet['user']['profile'] : ''; ?>" alt="" width="55px" height="55px" class="rounded-circle mr-3">
                        <div class="media-body">
                            <h5>Adja f BARRY</h5>
                            <p class="card-text text-justify">Aenean lacinia bibendum nulla sed consectetur. Vestibulum id ligula porta felis euismod semper. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>

                            <div class="row no-gutters mb-3">
                                <div class="col-6 p-1 text-center">
                                    <img src="img/adventure-alps-clouds-2259810.jpg" alt="" class="img-fluid mb-2">
                                    <img src="img/aerial-view-architectural-design-buildings-2228123.jpg" alt="" class="img-fluid">
                                </div>

                                <div class="col-6 p-1 text-center">
                                    <img src="img/celebration-colored-smoke-dark-2297472.jpg" alt="" class="img-fluid mb-2">
                                    <img src="img/bodybuilding-exercise-fitness-2294361.jpg" alt=""class="img-fluid">
                                </div>
                            </div>

                            <div class="media mb-3">
                                <img src="img/avatar-dhg.png" alt="img" width="45px" height="45px" class="rounded-circle mr-2">
                                <div class="media-body">
                                    <p class="card-text text-justify">Jacon Thornton: Donec id elit non mi porta gravida at eget metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Donec ullamcorper nulla non metus auctor fringilla. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Sed posuere consectetur est at lobortis.</p>
                                </div>
                            </div>

                            <div class="media">
                                <img src="img/avatar-mdo.png" alt="img" width="45px" height="45px" class="rounded-circle mr-2">
                                <div class="media-body">
                                    <p class="card-text text-justify">Mark Otto: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
                                </div>
                            </div>
                        </div>
                        <small>5min</small>
                    </div>
                </div>
                <hr>

                <div class="card-body">
                    <div class="media">
                        <img src="img/avatar-fat.jpg" alt="img" width="55px" height="55px" class="rounded-circle mr-3">

                        <div class="media-body">
                            <h5>Jacob Thornton</h5>
                            <p class="text-justify">Donec id elit non mi porta gravida at eget metus. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. 
                            Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                        <small>14 min</small>
                    </div>
                </div>
                <hr>

                <div class="card-body">
                    <div class="media">
                        <img src="img/avatar-mdo.png" alt="img" width="55px" height="55px" class="rounded-circle mr-3">
                        <div class="media-body">
                            <h5>Mark Otto</h5>
                            <p class="text-justify">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
                            <a href="img/mid5.jpg" data-lightbox="id"><img src="img/mid5.jpg" alt="" class="img-fluid shadow-sm img-thumbnail"></a>
                        </div>
                        <small class="text-muted">10 min</small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!------------------------Middle column Ends---------------->
    <!---------------------------Statrs Right Columns----------------->

    <div class="col-12 col-lg-3">
        <div class="right-column">

            <div class="card shadow-sm mb-4" >
                <div class="card-body">
                    <h6 class="card-title">Sponsored</h6>
                    <img src="img/right1.jpg" alt="card-img" class="card-img mb-3">
                    <p class="card-text text-justify"> <span class="h6">It might be time to visit Iceland.</span> Iceland is so chill, and everything looks cool here. Also, we heard the people are pretty nice.  What are you waiting for?</p>
                    <a href="#" class="btn btn-outline-info card-link btn-sm">Buy a ticket</a>

                </div>
            </div>
            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <h6 class="card-title ">Likes <a href="#" class="ml-1"><small>.View All</small> </a> </h6>
                    <div class="row no-gutters d-none d-lg-flex">
                        <div class="col-6 p-1">
                            <img src="img/avatar-dhg.png" alt="img" width="80px" height="80px" class="rounded-circle mb-4">
                            <img src="img/avatar-fat.jpg" alt="img" width="80px" height="80px" class="rounded-circle">

                        </div>
                        <div class="col-6 p-1 text-left">
                            <h6>Jacob Thornton @fat</h6>
                            <a href="#" class="btn btn-outline-info btn-sm mb-3"><i class="fas fa-user-friends"></i>Follow </a>

                            <h6>Mark otto</h6>
                            <a href="#" class="btn btn-outline-info  btn-sm"><i class="fas fa-user-friends"></i>Follow </a>

                        </div>

                    </div>

                </div>

                <div class="card-footer">

                    <p class="lead" style="font-size:18px;">Dave really likes these nerds, no one knows why though.</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <p>&copy; 2018 Bootstrap
                        <a href="#">About</a>
                        <a href="#">Help</a>
                        <a href="#">Terms</a>
                        <a href="#">Privacy</a>
                        <a href="#">Cookies</a>
                        <a href="#">Ads </a>
                        <a href="#">Info</a>
                        <a href="#">Brand</a>
                        <a href="#">Blog</a>
                        <a href="#">Status</a>
                        <a href="#">Apps</a>
                        <a href="#">Jobs</a>
                        <a href="#">Advertise</a>      
                    </p>
                </div>

            </div>
        </div>

    </div>

</div>

<!------------------------Light BOx OPtions------------->
<script>
    lightbox.option({

    })
    document.getElementById('photo-upload').addEventListener('change', function(event) {
        var selectedFile = event.target.files[0];
        var selectedPhoto = document.getElementById('selected-photo');

        var reader = new FileReader();
        reader.onload = function(event) {
            selectedPhoto.src = event.target.result;
            selectedPhoto.style.display = 'block';
        };

        reader.readAsDataURL(selectedFile);
    });

</script>
<!------------------------Light BOx OPtions------------->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Gestion du clic sur le bouton "like"
        $('.like-form').on('click', '.like-btn', function(e) {
            e.preventDefault();
            var tweetId = $(this).closest('.like-form').data('tweet-id');
            var likeBtn = $(this);
            var likeCount = $(this).find('.like-count');

            // Envoyer une requête AJAX pour mettre à jour le nombre de likes
            $.ajax({
                url: 'update_likes.php',
                type: 'POST',
                data: { tweet_id: tweetId },
                success: function(response) {
                    if (response.trim() !== 'Erreur lors de la mise à jour des likes.') {
                        // Mettre à jour l'affichage du nombre de likes
                        likeCount.text(response);
                        if (likeBtn.hasClass('liked')) {
                            likeBtn.removeClass('liked');
                        } else {
                            likeBtn.addClass('liked');
                        }
                    } else {
                        console.log('Erreur lors de la mise à jour des likes.');
                    }
                },
                error: function() {
                    console.log('Erreur lors de la requête AJAX.');
                }
            });
        });
    });
</script>



</body>
</html>