<?php
$database = json_decode(file_get_contents("database.json"), true);
$error = "";
$url = "";

if(isset($_GET['p'])){
    // this executes where there is a parameter
    goToShortUrl();
}

if(isset($_POST['submit'])){
    shortenURL();
}
// all code comes below here

function goToShortUrl(){
    global $database, $error;
    $shortUrl = $_GET['p'];
    if(checkShortURLExists($shortUrl)){
        echo "short url exists";
        header("Location: ".urldecode(getLongURL($shortUrl)));
    } else{
        $error = "This URL doesn't exist in the database.";
    }
}

function shortenURL(){
    global $database, $error, $url;
    $urlToShorten = urlencode($_POST["urlToShortenInput"]);
    if(!checkIfLongURLExists($urlToShorten)){
        // var_dump($database);
        $shortURLCode = generateShortCode();
        $url = "https://lilurl.link/". $shortURLCode;
        // echo $url;
        // echo $urlToShorten;
        // echo "<br>" . $shortURLCode;
        // var_dump($database);
        addToDatabase($urlToShorten, $shortURLCode, $database);
        // $error = "Success!";
    }else{
        $error = "This URL has already been added.";
    }
}

function generateShortCode(){
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $valid = false;
    while (!$valid){
        $shortString = "";
        for($i = 0; $i<6; $i++){
            $shortString .= $characters[rand(0, strlen($characters))];
        }
        if(!checkShortURLExists($shortString)){
            $valid = true;
        }
        // echo $shortString;
    }
    return $shortString;
}

// database functions (these all assume $database contains the decoded database and is accessible)
function getShortURL($longURL){
    global $database;
    foreach($database as $databaseItem){
        if ($databaseItem["longURL"] == $longURL){
            return $databaseItem["shortURL"];
        }
    }
}

function getLongURL($shortURL){
    global $database;
    foreach($database as $databaseItem){
        if ($databaseItem["shortURL"] == $shortURL){
            return $databaseItem["longURL"];
        }
    }
}

function checkIfLongURLExists($longURL){
    global $database;
    foreach($database as $databaseItem){
        if ($databaseItem["longURL"] == $longURL){
            return true;
        }
    }
    return false;
}

function checkShortURLExists($shortURLToCheck){
    global $database;
    foreach($database as $databaseItem){
        if ($databaseItem["shortURL"] == $shortURLToCheck){
            return true;
        }
    }
    return false;
}

function addToDatabase($longURL, $shortURL, $database){
    // global $database;
    array_push($database, array('longURL'=>$longURL, 'shortURL'=>$shortURL));
    file_put_contents("database.json", json_encode($database));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LilURL | A lil URL shortener</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>LilURL</h1>
        <p class="subtitle">A little URL shortener</p>
        <div class="central-box">
            <h2>Enter a URL below</h2>
            <p>and be amazed by the short URL which appears below it!</p>
            <form method="post" action="index.php">
                <input type="text" name="urlToShortenInput" placeholder="Enter URL to shorten here">
                <input type="submit" value="Go➡️" name="submit"> 
            </form>
            <?php
                if($url != ""){
                    // we need to display the url
                    echo "<div id='success-box' class='response-box url-box'>";
                        echo "<p><a href='" . $url . "' id='shortURLToCopy'>" . $url . "</a> <span class='pointer' onclick='copy()'>📋</span></p>";
                    echo "</div>";
                }
                if($error != ""){
                    echo "<div class='response-box error-box'>";
                        echo "<p>" . $error . "</p>";
                    echo "</div>";
                }
            ?>
        </div>
    </main>


    <script>
        function copy(){
            let copyText = document.getElementById('shortURLToCopy');

            navigator.clipboard.writeText(copyText.innerHTML);
            // alert(copyText.innerText.toString());

            let successBox = document.getElementById('success-box');
            let copySuccess = document.createElement('p');
            copySuccess.innerHTML = "Copied to clipboard!";
            successBox.append(copySuccess);
        }
    </script>

</body>
</html>
