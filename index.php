<?php
// Open the SQLite database
$db = new SQLite3('forum.db');

// Retrieve the posts
$results = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IC470 Forum</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }

        h1 {
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .post-list {
            list-style-type: none;
            padding: 0;
        }

        .post-list li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #e9e9e9;
            border-radius: 5px;
        }

        .post-list a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            cursor: pointer;
        }

        .post-list a:hover {
            color: #007bff;
        }

        .create-post-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .create-post-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>IC470 Forum</h1>

        <a href="create_post.html" class="create-post-button">Create Post</a>

       
        <?php while ($row = $results->fetchArray()): ?>
            <div>
                <h2><a href="postInfo.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
                <small>Posted on <?php echo $row['created_at']; ?></small>
            </div>
            <hr>
        <?php endwhile; ?>
    </div>`

</body>

</html>