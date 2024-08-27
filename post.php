<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Open the SQLite database
    $db = new SQLite3('forum.db');

    // Get the form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $content = $_POST['content'];

    // Prepare an SQL statement
    $stmt = $db->prepare("INSERT INTO posts (title, author, content) VALUES (:title, :author, :content)");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':author', $author, SQLITE3_TEXT);
    $stmt->bindValue(':content', $content, SQLITE3_TEXT);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Post saved successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Failed to save post.";
    }
}
?>
