# **Project schematic :**

## - `login.php`

### Used to Log in a player.

Redirections :  
    - register.php  
    - dashboard.php  

## - `register.php`

 Used to register a new player to the database  
 Redirections :   
     - login.php  

e## - `dbInterface.php`

### Used to interface with the database(s)

Every file that has to interact in some way with any stored data has to do it through this file.   
Every db-related function has to be written in this file.   

## - `auth.php`

### Used to handle user connections

Logins and registrations are done through this file, which itself calls `dbInterface.php`

## - `adminDashboard.php`

Accessible from the public home page, it checks for privileges then redirects to a dashboard to manage games and register new acocunts


