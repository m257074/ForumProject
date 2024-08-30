<?php
// Open the SQLite database
$db = new SQLite3('forum.db');

// Retrieve the posts
$results = $db->query("SELECT id, title, author, content, likes, dislikes, (likes - dislikes) 
AS net_likes, created_at FROM posts ORDER BY net_likes DESC, created_at ASC");
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

        .like-dislike {
            margin-top: 10px;
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
            <div class="post">
                <h2><a href="postInfo.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
                <p><strong>Author:</strong> <?php echo htmlspecialchars($row['author']); ?></p>
                <p><strong>Net Likes:</strong> <?php echo htmlspecialchars($row['net_likes']); ?></p>
                <p><small>Posted on <?php echo htmlspecialchars($row['created_at']); ?></small></p>
                <hr>
            </div>
        <?php endwhile; ?>
    </div>`

</body>

</html>