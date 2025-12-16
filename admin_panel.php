<?php
// Database Connection
require 'db_connection.php';

// Session Start and Admin Check
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check admin (using email-based admin as in your login)
if ($_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

// Handle Movie Add (URL-based)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {

    $movie_name   = $conn->real_escape_string($_POST['movie_name']);
    $movie_poster = $conn->real_escape_string($_POST['movie_poster']); // URL
    $movie_file   = $conn->real_escape_string($_POST['movie_file']);   // URL
    $movie_genres = implode(", ", $_POST['movie_genres']);
    $release_year = (int) $_POST['release_year'];
    $imdb_rating  = $conn->real_escape_string($_POST['imdb_rating']);
    $language     = $conn->real_escape_string($_POST['language']);
    $description  = $conn->real_escape_string($_POST['description']);

    if (
        empty($movie_name) || empty($movie_poster) || empty($movie_file)
    ) {
        echo "<script>alert('All fields are required');</script>";
    } else {
        $insert_query = "
            INSERT INTO movies (
                movie_name,
                movie_poster,
                movie_file,
                movie_genres,
                release_year,
                imdb_rating,
                language,
                description,
                upload_date
            ) VALUES (
                '$movie_name',
                '$movie_poster',
                '$movie_file',
                '$movie_genres',
                '$release_year',
                '$imdb_rating',
                '$language',
                '$description',
                NOW()
            )
        ";

        if ($conn->query($insert_query)) {
            echo "<script>alert('Movie added successfully!');</script>";
        } else {
            echo "<script>alert('Database error!');</script>";
        }
    }
}

// Handle Movie Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $movie_id = (int) $_POST['movie_id'];
    $conn->query("DELETE FROM movies WHERE id = $movie_id");
}

// Fetch Movies
$movies = $conn->query("SELECT * FROM movies ORDER BY upload_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Infinity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background:#121212; color:#fff; }
        .movie-grid { display:flex; flex-wrap:wrap; gap:20px; }
        .movie-card {
            background:#1e1e1e;
            width:250px;
            padding:15px;
            border-radius:10px;
            text-align:center;
        }
        .movie-card img {
            width:100%;
            height:300px;
            object-fit:cover;
            border-radius:8px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Admin Panel</h1>

    <!-- ADD MOVIE FORM -->
    <form method="POST" class="mb-5">
        <input type="hidden" name="action" value="upload">

        <div class="mb-3">
            <label class="form-label">Movie Name</label>
            <input type="text" name="movie_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Movie Poster URL</label>
            <input type="url" name="movie_poster" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Movie Video URL</label>
            <input type="url" name="movie_file" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Genres</label>
            <select name="movie_genres[]" class="form-control" multiple required>
                <option>Action</option>
                <option>Comedy</option>
                <option>Drama</option>
                <option>Horror</option>
                <option>Thriller</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Release Year</label>
            <input type="number" name="release_year" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">IMDB Rating</label>
            <input type="text" name="imdb_rating" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Language</label>
            <input type="text" name="language" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Add Movie</button>
    </form>

    <!-- MOVIE LIST -->
    <h2>Uploaded Movies</h2>
    <div class="movie-grid">
        <?php while ($m = $movies->fetch_assoc()): ?>
            <div class="movie-card">
                <img src="<?php echo $m['movie_poster']; ?>" alt="Poster">
                <h5 class="mt-2"><?php echo htmlspecialchars($m['movie_name']); ?></h5>
                <p><?php echo $m['movie_genres']; ?></p>
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="movie_id" value="<?php echo $m['id']; ?>">
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
