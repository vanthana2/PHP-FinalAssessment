<?php
require '../config/Database.php';
session_start();

$db = (new Database())->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];

    $query = "INSERT INTO posts (title, content, category_id) VALUES (:title, :content, :category_id)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header('Location: ../views/posts.php');
        exit();
    } else {
        echo "Error adding post";
    }
}

$categoryQuery = "SELECT * FROM categories";
$categoryStmt = $db->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 20px;
        }
        form {
            padding: 15px;
        }
        label {
            font-weight: bold;
            font-style: oblique;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            box-sizing: border-box;
            font-size: 16px;
        }
        label {
         font-weight: bold;
         margin-bottom: 5px;
         display: block;  
        }

        select {
         width: 100%;
         padding: 10px;
         border: 1px solid #ccc;
         border-radius: 4px;
         margin-bottom: 10px;
         box-sizing: border-box;
         font-size: 16px;
        }

       label[for="category"] {
         font-weight: bold;
        }

        button {
          background-color: #38a169;
          color: #fff;
          border: none;
          padding: 12px 20px;
          border-radius: 4px;
          cursor: pointer;
          font-size: 16px;
          transition: background-color 0.3s ease;
}

        button:hover {
          background-color: #2c7a52;
}
    </style>
    <title>Create Post</title>
</head>
<body>
    <div class="container">
        <h2>Create Post</h2>
        <form method="POST" action="">
            <div>
                <label>Title</label>
                <input type="text" name="title" required>
            </div>
            <div>
                <label>Content</label>
                <textarea name="content" required rows="4"></textarea>
            </div>
            <div>
             <label for="category">Category</label>
             <select id="category" name="category" required>
             <option value="">Select Category</option>
             <?php foreach ($categories as $category): ?>
               <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
    </select>
</div>

            <button type="submit">Add Post</button>
        </form>
    </div>
</body>
</html>
