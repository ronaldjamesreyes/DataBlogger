<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';
    if($_SESSION['auth']['username'] == 'john' && password_verify('pass1234', $_SESSION['auth']['password'])){
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
        $query = '';
        $sqlScript = file('database.sql');
        foreach ($sqlScript as $line)	{
            
            $startWith = substr(trim($line), 0 ,2);
            $endWith = substr(trim($line), -1 ,1);
            
            if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                continue;
            }
                
            $query = $query . $line;
            if ($endWith == ';') {
                if(!$db->prepare($query)->execute()) {
                    die("Problem in executing the SQL query $query");
                }
                $query= '';		
            }
        }
        $_SESSION['successMessage'] = "Database initialization done.";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['errorMessage'] = "Incorrect User. Must be a certain username with admin privileges to perform this action.";
        header('Location: index.php');
        exit();
    }
?>