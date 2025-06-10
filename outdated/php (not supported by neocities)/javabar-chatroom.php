<?php
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function loadBadWords($filename) {
    $badWords = array();

    // Check if file exists
    if (file_exists($filename)) {
        // reads file 
        $content = file_get_contents($filename);
        $badWords = explode("\n", $content);

        // del empty elements
        $badWords = array_filter($badWords, 'trim');
    }

    return $badWords;
}

function isProfane($text) {
    // Loads lists
    $enBadWords = loadBadWords('../badwordsblock/en.txt');
    $esBadWords = loadBadWords('../badwordsblock/es.txt');

    // Combi lists
    $profaneWords = array_merge($enBadWords, $esBadWords);

    foreach ($profaneWords as $word) {
        // stripos case compare
        if (stripos($text, $word) !== false) {
            return true;
        }
    }

    return false;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $color = sanitizeInput($_POST['color']);
    $message = sanitizeInput($_POST['message']);

    // Check evil words >:3
    if (isProfane($message) || isProfane($username)) {
        echo "<script>alert('Inappropriate content detected. Please keep the conversation respectful.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO ChatMessages (username, color, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $color, $message);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: $_SERVER[PHP_SELF]");
    exit(); // Make sure to exit after the header redirect
}

$sql = "SELECT * FROM ChatMessages ORDER BY timestamp DESC LIMIT 150";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Internet Cafe! | The Java bar☕</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <pre>
   ______  __  __   ______         __   ______   __   __ ______       ______   ______   ______    
  /\__  _\/\ \_\ \ /\  ___\       /\ \ /\  __ \ /\ \ / //\  __ \     /\  == \ /\  __ \ /\  == \   
  \/_/\ \/\ \  __ \\ \  __\      _\_\ \\ \  __ \\ \ \'/ \ \  __ \    \ \  __< \ \  __ \\ \  __<   
     \ \_\ \ \_\ \_\\ \_____\   /\_____\\ \_\ \_\\ \__|  \ \_\ \_\    \ \_____\\ \_\ \_\\ \_\ \_\ 
      \/_/  \/_/\/_/ \/_____/   \/_____/ \/_/\/_/ \/_/    \/_/\/_/     \/_____/ \/_/\/_/ \/_/ /_/ 
        </pre>
    </header>
    <div class="top-border">
        <main>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $username = htmlspecialchars($row['username']);
                    $color = htmlspecialchars($row['color']);
                    $message = htmlspecialchars($row['message']);
                    $timestamp = $row['timestamp'];

                    echo "<p style='color:$color;'>[$timestamp] $username: $message</p>";
                }
            } else {
                echo "<p>No messages posted.</p>";
            }
            ?>
            <div class="top-border">
                <form method="post">
                    <br>
                    <br>
                    <br>
                    Type a username! <input type="text" name="username" maxlength="12" required><br>
                    Color:
                    <select name="color" required>
                        <option value="#ff0000">Red</option>
                        <option value="#00ff00">Green</option>
                        <option value="#0000ff">Blue</option>
                        <option value="#ffff00">Yellow</option>
                        <option value="#ff00ff">Magenta</option>
                        <option value="#00ffff">Cyan</option>
                        <option value="#800080">Purple</option>
                        <option value="#ffa500">Orange</option>
                        <option value="#008000">Dark Green</option>
                        <option value="#8b4513">Saddle Brown</option>
                        <option value="#4682b4">Steel Blue</option>
                        <option value="#ff69b4">Hot Pink</option>
                        <option value="#800000">Maroon</option>
                    </select><br><br>
                    Post: <textarea name="message" maxlength="30" required></textarea><br>
                    <input type="submit" value="Send">
                    <br>
                    <br>
                    <a href="../html/index.html">Go back?</a>
                    <p>or..</p>
                    <a href="../php/lounge-room.php">Go to the Lounge room♨️</a><br>
                </form>
            </div> <!-- Top border end div --> 
        </main>
        <div class="bottomblinkies">
            <img src="../images/debian.gif" alt="Powered by Debian Linux blinkie">
            <img src="../images/b2.gif" alt="then perish blinkie">
            <img src="../images/s1.gif" alt="when life gives you lemons blinkie">
            <img src="../images/k15.gif" alt="010101010 blinkie">
            <img src="../images/0034-skull.gif" alt="cyberbully blinkie">
            <img src="../images/a14.gif" alt="i regret nothing blinkie">
            <img src="../images/h1.gif" alt="denied blinkie">
            <img src="../images/l1.gif" alt="shut up! blinkie">
            <img src="../images/t4.gif" alt="you suck! blinkie">
        </div>
    </div>

    <footer>
        <a href="terms.html">terms</a>
        <p>© 2023 No rights reserved. A Yordi production. Powered by my girlfriend's Raspberry Pi!</p>
    </footer>

</body>
</html>

<?php
$conn->close();
?>
