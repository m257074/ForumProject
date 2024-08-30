
<?php
// Open the SQLite database
try {
    $db = new SQLite3('forum.db');
} catch (Exception $e) {
    die("Could not open database: " . $e->getMessage());
}

// Get the post ID from the URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle like/dislike actions for post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['like_post']) || isset($_POST['dislike_post']))) {
    $column = isset($_POST['like_post']) ? 'likes' : 'dislikes';
    $stmt = $db->prepare("UPDATE posts SET $column = $column + 1 WHERE id = :id");
    $stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
    $stmt->execute();
}

/* Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_comment'])) {
    $comment = $_POST['comment'];
    $stmt = $db->prepare("INSERT INTO comments (post_id, comment) VALUES (:post_id, :comment)");
    $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
    $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
    $stmt->execute();
}

// Retrieve comments for this post
$stmt = $db->prepare("SELECT *, (likes - dislikes) AS net_likes FROM comments WHERE post_id = :post_id ORDER BY net_likes DESC, created_at DESC");
$stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
$comments = $stmt->execute(); */

// Handle like/dislike actions for comments
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['like_comment']) || isset($_POST['dislike_comment']))) {
    $column = isset($_POST['like_comment']) ? 'likes' : 'dislikes';
    $comment_id = (int)$_POST['comment_id'];
    $stmt = $db->prepare("UPDATE comments SET $column = $column + 1 WHERE id = :id");
    $stmt->bindValue(':id', $comment_id, SQLITE3_INTEGER);
    $stmt->execute();
}

// Retrieve the post from the database
$stmt = $db->prepare("SELECT *, (likes - dislikes) AS net_likes FROM posts WHERE id = :id");
$stmt->bindValue(':id', $post_id, SQLITE3_INTEGER);
$post = $stmt->execute()->fetchArray();

// Handle new comment or reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_comment'])) {
    $comment = $_POST['comment'];
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    $stmt = $db->prepare("INSERT INTO comments (post_id, comment, parent_comment_id) VALUES (:post_id, :comment, :parent_id)");
    $stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
    $stmt->bindValue(':comment', $comment, SQLITE3_TEXT);
    $stmt->bindValue(':parent_id', $parent_id, SQLITE3_INTEGER);
    $stmt->execute();
}

// Fetch comments and replies
$stmt = $db->prepare("SELECT *, (likes - dislikes) AS net_likes FROM comments WHERE post_id = :post_id ORDER BY net_likes DESC, created_at DESC");
$stmt->bindValue(':post_id', $post_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$comments = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $comments[] = $row;
}

if (!$post) {
    die("Post not found!");
}

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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .comment {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #007BFF;
        }
        .like-dislike {
            margin-top: 10px;
        }
        .like-dislike button {
            padding: 5px 10px;
            margin-right: 5px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .like-dislike button.dislike {
            background-color: #FF0000;
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
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-button">Back to Forum</a>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><?php echo htmlspecialchars($post['content']); ?></p>
    <div class="like-dislike">
        <form method="POST">
            <button name="like_post">Like</button>
            <button name="dislike_post" class="dislike">Dislike</button>
        </form>
        <p>Net likes: <?php echo $post['net_likes']; ?></p>
    </div>
    <h2>Comments</h2>
    <?php foreach ($comments as $comment): ?>
        <?php if ($comment['parent_comment_id'] == null): ?>
            <div class="comment-box" style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                <div class="like-dislike">
                    <form method="POST">
                        <button name="like_comment">Like</button>
                        <button name="dislike_comment" class="dislike">Dislike</button>
                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                    </form>
                    <p>Net likes: <?php echo $comment['net_likes']; ?></p>
                </div>
                <!-- Reply form for each comment -->
                <form method="POST" class="reply-form" style="margin-top: 10px;">
                    <input type="hidden" name="parent_id" value="<?php echo $comment['id']; ?>">
                    <textarea name="comment" required placeholder="Write a reply..."></textarea>
                    <button type="submit" name="new_comment">Reply</button>
                </form>
            </div>
            <!-- Display replies -->
            <?php foreach ($comments as $reply): ?>
                <?php if ($reply['parent_comment_id'] == $comment['id']): ?>
                    <div class="reply-box" style="margin-left: 20px; border: 1px solid #eee; padding: 10px;">
                        <p><?php echo htmlspecialchars($reply['comment']); ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <h3>Add a comment:</h3>
    <form method="POST">
        <textarea name="comment" rows="4" style="width: 100%;"></textarea><br><br>
        <button type="submit" name="new_comment">Submit</button>
    </form>
</div>
</body>
</html>
