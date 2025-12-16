<?php
require 'db_connection.php';
session_start();

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate movie ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid movie selection.");
}

$movie_id = (int) $_GET['id'];

// Fetch movie
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Movie not found.");
}

$movie = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($movie['movie_name']); ?> - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<style>
body {
    background:#121212;
    color:#fff;
}
.movie-container {
    max-width: 1000px;
    margin: auto;
    padding: 20px;
}
.movie-poster {
    width: 300px;
    height: 450px;
    object-fit: cover;
    border-radius: 10px;
}
.movie-details {
    margin-top: 20px;
}
iframe {
    border-radius: 10px;
}
</style>
</head>

<body>

<!-- Loader -->
<div id="loading-screen">
    <div class="spinner-box">
        <div class="circle-border">
            <div class="circle-core"></div>
        </div>
        <h3>Loading, please wait...</h3>
    </div>
</div>

<div class="movie-container">

    <!-- Movie Info -->
    <div class="row g-4 align-items-start">
        <div class="col-md-4 text-center">
            <img
                src="<?php echo htmlspecialchars($movie['movie_poster']); ?>"
                class="movie-poster"
                onerror="this.src='elements/default-poster.png';"
            >
        </div>

        <div class="col-md-8 movie-details">
            <h2><?php echo htmlspecialchars($movie['movie_name']); ?></h2>
            <p><strong>Genres:</strong> <?php echo $movie['movie_genres']; ?></p>
            <p><strong>Release Year:</strong> <?php echo $movie['release_year']; ?></p>
            <p>
                <strong>IMDb:</strong>
                <img src="elements/logo/imdb.png" style="height:18px;">
                <?php echo $movie['imdb_rating']; ?>
            </p>
            <p><strong>Language:</strong> <?php echo $movie['language']; ?></p>
            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
        </div>
    </div>

    <!-- Player -->
    <div class="mt-5">
        <h4>Watch Now</h4>

        <!-- OPTION 1: IFRAME (BEST FOR CLOUD LINKS) -->
        <iframe
            src="<?php echo htmlspecialchars($movie['movie_file']); ?>"
            width="100%"
            height="500"
            allowfullscreen
            loading="lazy">
        </iframe>

        <!--
        OPTION 2 (ONLY if your cloud supports direct MP4):
        <video width="100%" height="500" controls>
            <source src="<?php echo htmlspecialchars($movie['movie_file']); ?>" type="video/mp4">
        </video>
        -->
    </div>

</div>

<script src="script.js"></script>
</body>
</html>
