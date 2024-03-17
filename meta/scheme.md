# **Project schematic :**

## - `login.php`
### Logs in a player.

Redirections :  
    - register.php  
    - home.php  

## - `register.php`
### Registers a player

Redirections :   
     - login.php  

## - `logout.php`
### Logs out a player

Redirections :   
     - login.php  

## - `dbInterface.php`
### Interfaces with the database(s)

Every file that has to interact in some way with any stored data has to do it through this file.   
Every db-related function has to be written in this file.   

## - `auth.php`
### Handles user connections

Logins and registrations are done through this file, which itself calls `dbInterface.php`.


## - `adminDashboard.php`

Accessible from the public home page, it checks for privileges then redirects to a dashboard to manage games and register new acocunts

## - `utils.php`

Collection of utilitarian reusable code throughout the project.

## - `EStatus.php`

Enum of status used by dbInterface to handle multiple outputs.


