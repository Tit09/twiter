<!-- <?php
// Inclure le fichier de configuration de la base de données
// 

// Vérifier si le formulaire de publication a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si le champ de contenu n'est pas vide
    if (!empty($_POST['content'])) {
        $content = $_POST['content'];
        
        // Vérifier s'il y a une image sélectionnée
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            
            // Déplacer l'image téléchargée vers le dossier des images
            move_uploaded_file($image_tmp, 'uploads/' . $image);
        } else {
            $image = null;
        }
        
        // Récupérer l'ID de l'utilisateur actuel (vous devez implémenter cela selon votre système d'authentification)
        $user_id = 1; // Exemple : ID de l'utilisateur connecté
        
        // Insérer la publication dans la base de données
        $sql = "INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $content, $image);
        
        if (mysqli_stmt_execute($stmt)) {
            // La publication a été créée avec succès
            echo "Publication créée avec succès!";
        } else {
            // Une erreur s'est produite lors de la création de la publication
            echo "Erreur lors de la création de la publication : " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        // Le champ de contenu est vide
        echo "Veuillez saisir du contenu pour la publication.";
    }
}
?>
 -->