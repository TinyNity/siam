<?php

class EStatus {
    //! User Related Status
    const APPROVED = "Authentification approved";
    const REJECTED = "Authentification rejected";
    const NOUSER = "User not found";
    const USERINDB = "User already in database";
    const USERCREATED = "User successfully created";
    //! Game Database Related Status 
    const NOGAME = "Game not found";
    const GAMECREATED = "Game successfully created";
    const GAMEISFULL = "Game is full";
    const GAMEBOARDCREATED = "Gameboard successfully created"; 
    const GAMEBOARDUPDATED = "Gameboard successfully updated";
    const GAMEBOARDUPDATEFAILED = "Gameboard could not update";
    //! Player Related Status
    const PLAYERADDED = "Player successfully added";
    const PLAYERINGAME = "Player is in game";
    const PLAYERNOTINGAME = "Player is not in game";
    const NOPLAYER = "Player not found";
    const USERISPLAYER = "User is a player";
    const USERISNOTPLAYER = "User is not a player";
    }