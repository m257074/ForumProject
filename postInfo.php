<?php
// Open the SQLite database
try {
    $db = new SQLite3('forum.db');
} catch (Exception $e) {
    die("Could not open database: " . $e->getMessage());
}

// Get the post ID from the URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Retrieve the post from the database
$stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
$post = $stmt->execute()->fetchArray();

if (!$post) {
    die("Post not found!");
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $stmt = $db->prepare("INSERT INTO comments (post_id, comment) VALUES (:post_id, :comment)");
    $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
    $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
    $stmt->execute();
}

// Retrieve comments for this post
$stmt = $db->prepare("SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at DESC");
$stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
$comments = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .post-content {
            margin-top: 20px;
        }
        .comment-section {
            margin-top: 30px;
        }
        .comment-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .comment-form input[type="text"],
        .comment-form textarea {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 100%;
        }
        .comment-form textarea {
            resize: vertical;
            height: 100px;
        }
        .comment-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .comment-form button:hover {
            background-color: #0056b3;
        }
        .comment-list {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }
        .comment-list li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #e9e9e9;
            border-radius: 5px;
        }
        .comment-list p {
            margin: 5px 0;
        }
        a.back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 id="postTitle"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><strong>Author:</strong> <span id="postAuthor"><?php echo nl2br(htmlspecialchars($post['author'])); ?></span></p>
        <small>Posted on <?php echo $post['created_at']; ?></small>
        <div class="post-content">
            <p id="postContent"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>

        <div class="comment-section">
            <h2>Comments</h2>
            <ul class="comment-list" id="commentList">
            <?php while ($comment = $comments->fetchArray()): ?>
                <div>
                <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                <small>Posted on <?php echo $comment['created_at']; ?></small>
                </div>
                <hr>
            <?php endwhile; ?>
            </ul>
        </div>

        <div class="comment-form">
            <h3>Add a Comment</h3>
            <form id="commentForm" action="postInfo.php?id=<?php echo $post_id; ?>" method="POST">
                
                <!--<label for="commentAuthor">Your Name</label>
                <input type="text" id="commentAuthor" name="commentAuthor" placeholder="Enter your name" required>-->

                <label for="comment">Comment</label>
                <textarea id="comment" name="comment" placeholder="Write your comment here" required></textarea>

                <button type="submit">Submit Comment</button>
            </form>
        </div>

        <a href="index.php" class="back-button">Back to Forum</a>
    </div>
</body>
</html>

