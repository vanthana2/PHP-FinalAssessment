<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Post List</title>
    <style>
        body{
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding-top: 50px;
        }
        #controls {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #controls form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #controls button {
            background-color: #38a169;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        #controls button:hover {
            background-color: #2c7a52;
        }
        #pagination {
            margin-top: 20px;
            text-align: center;
        }
        #pagination a {
            margin: 0 5px;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #pagination a:hover {
            background-color: #434343;
            color: #fff;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto pt-10">
        <div id="controls" class="mb-5 flex justify-between items-center">
            <form method="POST" action="" class="flex items-center space-x-4">
                <input type="text" name="search" placeholder="Search posts" class="border border-gray-300 rounded px-4 py-2">
                <select name="category" class="border border-gray-300 rounded px-4 py-2">
                    <option value="">All Categories</option>
                    <?php
                    require '../config/Database.php';
                    $db = (new Database())->getConnection();

                    $categoryQuery = "SELECT * FROM categories";
                    $categoryStmt = $db->prepare($categoryQuery);
                    $categoryStmt->execute();
                    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($categories as $category) {
                        echo "<option value='{$category['id']}'>{$category['name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="bg-blue-500 text-white rounded px-4 py-2 hover:bg-blue-600 transition">Filter</button>
            </form>
            <div class="flex space-x-4">
                <?php
                session_start();
                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                    echo "<a href='create_post.php' class='bg-green-500 text-white rounded px-4 py-2 hover:bg-green-600 transition'>Create Post</a>";
                }
                ?>
                <a href="logout.php" class="bg-red-500 text-white rounded px-4 py-2 hover:bg-red-600 transition">Logout</a>
            </div>
        </div>
        <div id="postList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 bg-white shadow-md rounded-lg p-5">
            <?php
            $search = $_POST['search'] ?? '';
            $categoryFilter = $_POST['category'] ?? '';
            $page = $_GET['page'] ?? 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

            $query = "SELECT posts.*, categories.name AS category_name 
                      FROM posts 
                      JOIN categories ON posts.category_id = categories.id 
                      WHERE posts.title LIKE :search";

            if ($categoryFilter) {
                $query .= " AND posts.category_id = :category";
            }

            $query .= " LIMIT :offset, :limit";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            if ($categoryFilter) {
                $stmt->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($posts) {
                foreach ($posts as $postData) {
                    echo "<div class='item border border-gray-300 rounded-lg p-3 shadow-sm hover:shadow-md transition duration-200'>
                            <h3 class='text-lg font-semibold'>{$postData['title']}</h3>
                            <p class='text-gray-600'>{$postData['content']}</p>
                            <p class='text-gray-500'>Category: {$postData['category_name']}</p>
                        </div>";
                }
            } else {
                echo "<p class='text-gray-500'>No posts found for the selected filters.</p>";
            }
            ?>
        </div>
        <div id="pagination" class="mt-5">
            <?php
            $totalQuery = "SELECT COUNT(*) FROM posts WHERE title LIKE :search";
            if ($categoryFilter) {
                $totalQuery .= " AND category_id = :category";
            }
            $totalStmt = $db->prepare($totalQuery);
            $totalStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            if ($categoryFilter) {
                $totalStmt->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
            }
            $totalStmt->execute();
            $totalPosts = $totalStmt->fetchColumn();
            $totalPages = ceil($totalPosts / $limit);

            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='?page=$i' class='mx-1 px-3 py-1 border border-gray-300 rounded hover:bg-blue-500 hover:text-white transition duration-200'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>
</html>

