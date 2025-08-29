<?php
// Database connection
require_once 'db.php';
 
// Function to fetch news articles
function getNews($conn, $category = null, $limit = 10) {
    $sql = "SELECT * FROM news_articles";
    if ($category) {
        $sql .= " WHERE category = ?";
    }
    $sql .= " ORDER BY publish_date DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    if ($category) {
        $stmt->bind_param("si", $category, $limit);
    } else {
        $stmt->bind_param("i", $limit);
    }
    $stmt->execute();
    return $stmt->get_result();
}
 
// Function to fetch single article
function getArticle($conn, $id) {
    $sql = "SELECT * FROM news_articles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
 
// Determine page type
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$category = isset($_GET['category']) ? $_GET['category'] : null;
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Website - CNN Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
 
        body {
            background-color: #f5f5f5;
        }
 
        header {
            background-color: #c00;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
 
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
 
        nav {
            background-color: #333;
            padding: 10px;
        }
 
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
            transition: color 0.3s;
        }
 
        nav a:hover {
            color: #c00;
        }
 
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
 
        .featured {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
 
        .featured h2 {
            color: #c00;
            margin-bottom: 15px;
        }
 
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
 
        .news-item {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
 
        .news-item:hover {
            transform: translateY(-5px);
        }
 
        .news-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
 
        .news-item h3 {
            padding: 15px;
            font-size: 1.2em;
            color: #333;
        }
 
        .news-item p {
            padding: 0 15px 15px;
            color: #666;
        }
 
        .article {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
 
        .article h1 {
            color: #c00;
            margin-bottom: 20px;
        }
 
        .article img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
 
        .related-news {
            margin-top: 20px;
        }
 
        .related-news h3 {
            color: #c00;
            margin-bottom: 15px;
        }
 
        .error-404 {
            text-align: center;
            padding: 50px 20px;
            background-color: #e0f7fa;
            color: #333;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
 
        .error-404 h2 {
            font-size: 3em;
            color: #007bff;
            margin-bottom: 20px;
        }
 
        .error-404 p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
 
        .error-404 a {
            color: #c00;
            text-decoration: none;
            font-weight: bold;
        }
 
        @media (max-width: 768px) {
            .news-grid {
                grid-template-columns: 1fr;
            }
 
            header h1 {
                font-size: 1.8em;
            }
 
            nav a {
                margin: 0 10px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>News Website</h1>
        <nav>
            <a href="javascript:navigate('home')">Home</a>
            <a href="javascript:navigate('category', 'World')">World</a>
            <a href="javascript:navigate('category', 'Sports')">Sports</a>
            <a href="javascript:navigate('category', 'Technology')">Technology</a>
            <a href="javascript:navigate('category', 'Entertainment')">Entertainment</a>
        </nav>
    </header>
 
    <div class="container">
        <?php if ($page === 'home'): ?>
            <div class="featured">
                <h2>Featured News</h2>
                <div class="news-grid">
                    <?php
                    $news = getNews($conn, null, 4);
                    if ($news->num_rows > 0) {
                        while ($row = $news->fetch_assoc()):
                    ?>
                        <div class="news-item">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <h3><a href="javascript:navigate('article', <?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                        </div>
                    <?php endwhile;
                    } else { ?>
                        <div class="error-404">
                            <h2>404 - Not Found</h2>
                            <p>We searched the news, but couldn't find any articles. <a href="javascript:navigate('home')">Return to Home</a></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php elseif ($page === 'category'): ?>
            <div class="featured">
                <h2><?php echo htmlspecialchars($category); ?> News</h2>
                <div class="news-grid">
                    <?php
                    $news = getNews($conn, $category);
                    if ($news->num_rows > 0) {
                        while ($row = $news->fetch_assoc()):
                    ?>
                        <div class="news-item">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <h3><a href="javascript:navigate('article', <?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                        </div>
                    <?php endwhile;
                    } else { ?>
                        <div class="error-404">
                            <h2>404 - Not Found</h2>
                            <p>No articles found in the <?php echo htmlspecialchars($category); ?> category. <a href="javascript:navigate('home')">Return to Home</a></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php elseif ($page === 'article' && $article_id): ?>
            <?php
            $article = getArticle($conn, $article_id);
            if ($article):
            ?>
                <div class="article">
                    <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                    <div class="related-news">
                        <h3>Related News</h3>
                        <div class="news-grid">
                            <?php
                            $related = getNews($conn, $article['category'], 3);
                            $relatedFound = false;
                            while ($row = $related->fetch_assoc()):
                                if ($row['id'] != $article_id):
                                    $relatedFound = true;
                            ?>
                                <div class="news-item">
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                    <h3><a href="javascript:navigate('article', <?php echo $row['id']; ?>)"><?php echo htmlspecialchars($row['title']); ?></a></h3>
                                    <p><?php echo htmlspecialchars(substr($row['content'], 0, 100)) . '...'; ?></p>
                                </div>
                            <?php
                                endif;
                            endwhile;
                            if (!$relatedFound) {
                                echo '<p>No related articles found.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-404">
                    <h2>404 - Not Found</h2>
                    <p>The article you’re looking for couldn’t be found. <a href="javascript:navigate('home')">Return to Home</a></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="error-404">
                <h2>404 - Not Found</h2>
                <p>We searched the news, but couldn’t find the page you’re looking for. <a href="javascript:navigate('home')">Return to Home</a></p>
            </div>
        <?php endif; ?>
    </div>
 
    <script>
        function navigate(page, param = null) {
            let url = '?page=' + page;
            if (page === 'category') {
                url += '&category=' + encodeURIComponent(param);
            } else if (page === 'article') {
                url += '&id=' + param;
            }
            window.location.href = url;
        }
    </script>
</body>
</html>
