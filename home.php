<?php
require 'db_connection.php';
session_start();

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

// Fetch movies
$sql = "
    SELECT 
        id,
        movie_name,
        movie_poster,
        movie_genres,
        release_year,
        imdb_rating,
        language
    FROM movies
";

if (!empty($search)) {
    $sql .= " 
        WHERE movie_name LIKE '%$search%' 
        OR movie_genres LIKE '%$search%'
    ";
}

$sql .= " ORDER BY upload_date DESC";

$movies = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Home - Infinity</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
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

<!-- Header -->
<div class="header d-flex justify-content-between align-items-center p-3 bg-dark text-white">
    <div class="logo fw-bold fs-4">Infinity</div>

    <div class="d-flex align-items-center gap-3">
        <form method="GET" class="d-flex">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search movies..." value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-primary btn-sm ms-2">Search</button>
        </form>

        <a href="home.php" class="text-white text-decoration-none">Home</a>
        <a href="profile.php" class="text-white text-decoration-none">Profile</a>
        <a href="history.php" class="text-white text-decoration-none">History</a>
        <a href="about.php" class="text-white text-decoration-none">About</a>
        <a href="logout.php" class="text-danger text-decoration-none">Logout</a>
    </div>
</div>

<!-- Movie Grid -->
<div class="container mt-4">
    <div class="row g-4">

        <?php if ($movies && $movies->num_rows > 0): ?>
            <?php while ($m = $movies->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card h-100 shadow">

                        <a href="movie.php?id=<?php echo $m['id']; ?>">
                            <img
                                src="<?php echo $m['movie_poster']; ?>"
                                class="card-img-top"
                                style="height:300px;object-fit:cover;"
                                onerror="this.src='elements/default-poster.png';"
                            >
                        </a>

                        <div class="card-body text-center">
                            <h6 class="fw-bold"><?php echo htmlspecialchars($m['movie_name']); ?></h6>
                            <p class="mb-1"><?php echo $m['movie_genres']; ?></p>
                            <p class="mb-1">Year: <?php echo $m['release_year']; ?></p>
                            <p class="mb-0">
                                <img src="elements/logo/imdb.png" style="height:16px;">
                                <?php echo $m['imdb_rating']; ?>
                            </p>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No movies found.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="script.js"></script>
</body>
</html>
