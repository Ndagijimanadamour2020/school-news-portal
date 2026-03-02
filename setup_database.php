<?php
require_once 'includes/config.php';

// Function to safely execute queries
function executeQuery($conn, $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "Success: " . substr($sql, 0, 50) . "...<br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
}

// 1. Add 'views' column to 'news' table if it doesn't exist
$check_views = mysqli_query($conn, "SHOW COLUMNS FROM news LIKE 'views'");
if (mysqli_num_rows($check_views) == 0) {
    executeQuery($conn, "ALTER TABLE news ADD COLUMN views INT DEFAULT 0");
} else {
    echo "Column 'views' already exists in 'news'.<br>";
}

// 2. Create 'comments' table
$sql_comments = "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
)";
executeQuery($conn, $sql_comments);

// 4. Sample Categories
$categories = [
    ['Events', 'School events and celebrations'],
    ['Announcements', 'Important announcements for students and parents'],
    ['Sports', 'News about sports activities'],
    ['Academics', 'Academic achievements and curriculum updates'],
    ['Clubs', 'Updates from various school clubs and societies']
];

foreach ($categories as $cat) {
    $name = $cat[0];
    $desc = $cat[1];
    $check = mysqli_query($conn, "SELECT id FROM categories WHERE name = '$name'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO categories (name, description) VALUES ('$name', '$desc')");
    }
}

// 5. Sample News Articles
$sample_news = [
    ['Annual Science Fair 2026', 'Our students showcased amazing projects ranging from robotics to sustainable energy solutions at this year\'s Science Fair...', 'Academics'],
    ['Football Team Wins Regional Finals', 'The school\'s football team, The Eagles, secured a stunning 3-1 victory against St. Peters in the regional finals yesterday...', 'Sports'],
    ['New Library Hours for Exam Season', 'To support our students during the upcoming mid-term exams, the library will extend its hours until 8:00 PM starting next Monday...', 'Announcements'],
    ['Spring Music Concert Next Friday', 'Join us for an evening of classical and contemporary music performed by the school orchestra and choir in the main hall...', 'Events'],
    ['Robotics Club: Call for New Members', 'Are you interested in building the future? The Robotics Club is looking for new members to join our competition team...', 'Clubs'],
    ['Math Olympiad Results Announced', 'Congratulations to Sarah Jenkins for securing the first position in the state-level Math Olympiad held last weekend...', 'Academics'],
    ['Inter-School Debate Competition', 'The debate team is preparing for the national inter-school debate competition. Good luck to our participants!', 'Events'],
    ['New Canteen Menu Released', 'We are excited to introduce a healthier and more diverse menu in the school canteen, featuring more vegan and gluten-free options...', 'Announcements']
];

foreach ($sample_news as $news) {
    $title = mysqli_real_escape_string($conn, $news[0]);
    $content = mysqli_real_escape_string($conn, $news[1]);
    $cat_name = $news[2];
    
    $check = mysqli_query($conn, "SELECT id FROM news WHERE title = '$title'");
    if (mysqli_num_rows($check) == 0) {
        $cat_id_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM categories WHERE name = '$cat_name'"));
        $cat_id = $cat_id_row['id'];
        mysqli_query($conn, "INSERT INTO news (title, content, category_id, views) VALUES ('$title', '$content', $cat_id, " . rand(10, 500) . ")");
    }
}

echo "Database setup and sample data injection complete! <a href='index.php'>Go to Home</a>";
?>