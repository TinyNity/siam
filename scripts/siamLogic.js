const BOARD_SIZE = 5;
const MAX_PLAYER = 2;
const id_player = parseInt(document.getElementById("id_player").value);

//? Ajax functions

function getGameData(targetGame) {
    $.ajax({
        url: 'sendGame.php',
        type: 'GET',
        async:false,
        dataType: 'json',
        success: function (data) {
            targetGame.initGame(data);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    })
}

function getPlayerData(targetGame) {
    $.ajax({
        url: 'sendPlayer.php',
        type: 'GET',
        async:false,
        dataType: 'json',
        success: function (data) {
            targetGame.initPlayer(data);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    })
}

function getGameboardData(targetGame) {
    $.ajax({
        url: 'sendGameboard.php',
        type: "GET",
        async:false,
        dataType: "json",
        success: function (data) {
            targetGame.initGameboard(data);
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    })
}

function sendPlayerData(targetPlayer){
    $.ajax({
        url:'php/updatePlayerData.php',
        type:"POST",
        data : {
            id_player: targetPlayer.id,
            reserved_piece:targetPlayer.reservedPiece
        },
        error: function(xhr,status,error){
            console.error(error);
        }
    })
}

function sendGameStatus(targetGame){
    $.ajax({
        url: 'php/updateGameStatus.php',
        type:"POST",
        data : {
            current_player_turn: targetGame.playerTurn,
            status: targetGame.status,
            winner: targetGame.winner
        },
        error: function(xhr,status,error){
            console.error(error);
        }
    })
}

function sendGameboard(targetGame) {
    for (let i = 0; i < BOARD_SIZE; i++) {
        for (let j = 0; j < BOARD_SIZE; j++) {
            sendGameboardCell(targetGame.gameboard[i][j]);
        }
    }
}

function sendGameboardCell(cell) {
    $.ajax({
        url: 'php/updateGameboardCell.php',
        type: "POST",
        data: {
            row: cell.row,
            column: cell.column,
            id_piece: cell.piece,
            id_player: cell.player,
            direction: cell.direction
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    })
}

function addNeighbors(file,cell,gameboard){
    let row=cell.row;
    let column=cell.column;
    for (let i=0;i<=3;i++){
        tempRow=row+Direction.getDirectionRow(i);
        tempColumn=column+Direction.getDirectionColumn(i);
        if (tempRow>=0 && tempRow<BOARD_SIZE && tempColumn>=0 && tempColumn<BOARD_SIZE){
            tempCell=gameboard[tempRow][tempColumn];
            file.push(tempCell);
        }
    }
    return file;
}

class GameStatus {
    static NOT_STARTED = 0;
    static STARTED = 1;
    static FINISHEDWIN = 2;
    static FINISHEDDRAW=3;

}

//! DATABASE NEEDS TO STORE THE SAME VALUE FOR DIRECTION AND PIECE AS THE VALUES IN THIS SCRIPT
class Direction {
    static UP = 0;
    static RIGHT = 1;
    static DOWN = 2;
    static LEFT = 3;

    //? Convert a direction to the corresponding x value
    static getDirectionRow(direction) {
        switch (direction) {
            case Direction.UP:
                return -1;
            case Direction.DOWN:
                return 1;
            default:
                return 0;
        }
    }

    //? Convert a direction to the corresponding y value
    static getDirectionColumn(direction) {
        switch (direction) {
            case Direction.RIGHT:
                return 1;
            case Direction.LEFT:
                return -1;
            default:
                return 0;
        }
    }
}

class Piece {
    static VOID = null;
    static ROCK = 1;
    static ELEPHANT = 2;
    static RHINOCEROS = 3;
}

class Cell {
    constructor(piece, direction, player, row, column) {
        this.piece = piece;
        this.direction = direction;
        this.player = player;
        this.row = row;
        this.column = column;
    }


    move(row, column) {
        this.row = row;
        this.column = column;
        return this;
    }


    //? Function to create a void cell at the same position of the cell
    void() {
        return new Cell(Piece.VOID, null, null, this.row, this.column);
    }

    rotate() {
        this.direction = (this.direction + 1) % 4;
    }

    //? Function to check if a cell can access another one (must be at a distance of 1 square)
    canAccess(cell) {
        if (Math.abs(this.row - cell.row) == 1 && this.column - cell.column == 0) {
            return true;
        }
        else if (Math.abs(this.column - cell.column) == 1 && this.row - cell.row == 0) {
            return true;
        }
        return false;
    }

    //? Function to retrieve the direction of the vector to go from current cell to targeted cell
    getMovementDirection(cell) {
        let difrow = cell.row - this.row;
        let difcolumn = cell.column - this.column;
        if (difrow >= 1) return Direction.DOWN;
        else if (difrow <= -1) return Direction.UP;
        else if (difcolumn >= 1) return Direction.RIGHT;
        else if (difcolumn <= -1) return Direction.LEFT;
    }

    //? Function to get the push strength depending on the piece direction and the direction it is pushed
    getPushStrength(directionPush) {
        let pushFactor = Math.abs(directionPush - this.direction); // If the push factor is equal to 2 then the pieces are opposite, if it's equal to 0 then it is the same piece, otherwise the piece is anything else
        if (this.piece == Piece.ROCK) {
            return -1;
        }
        if (pushFactor == 2) {
            return -2;
        }
        else if (pushFactor == 0) {
            return 2;
        }
        return 0;
    }


    assignTemporaryPosition(destinationCell) {
        if (destinationCell.row != null && destinationCell != null) {
            this.row = destinationCell.row - Direction.getDirectionRow(this.direction);
            this.column = destinationCell.column - Direction.getDirectionColumn(this.direction);
            return (this.row >= BOARD_SIZE || this.row < 0 || this.column >= BOARD_SIZE || this.column < 0);
        }
        return false;
    }

    calcDistance(baseCell){
        let distance=0;
        let tempRow=this.row;
        let tempColumn=this.column;
        while (baseCell.row!=tempRow){
            tempRow+=(baseCell.row-tempRow<0)?(-1):(1);
            distance++;
        }

        while (baseCell.column!=tempColumn){
            tempColumn+=(baseCell.column-tempColumn<0)?(-1):(1);
            distance++;
        }
        return distance;
    }
}

class Player {
    constructor(id, reservedPiece, typePiece,username) {
        this.id = id;
        this.reservedPiece = reservedPiece;
        this.typePiece = typePiece;
        this.isAddingPiece = false;
        this.username=username;
    }
}

class Siam {
    constructor() {
        this.gameboard = null;
        this.selectedCell = null;
        this.players = [];
        this.moveDone = false;
        this.playerTurn = null;
        this.pushDone = false;
        this.status = null;
        this.winner=null;
    }

    //? Function to initialize a gameboard
    initGameboard(gameboardData) {
        this.gameboard = [];
        for (let i = 0; i < BOARD_SIZE; i++) {
            this.gameboard.push([]);
            for (let j = 0; j < BOARD_SIZE; j++) {
                let gameboardCell = gameboardData[i * BOARD_SIZE + j];
                this.gameboard[gameboardCell.row][gameboardCell.column] = new Cell(gameboardCell.id_piece, gameboardCell.direction, gameboardCell.id_player, gameboardCell.row, gameboardCell.column);
            }
        }
    }

    initPlayer(playerData) {
        this.players=[];
        for (let i = 0; i < Math.min(playerData.length, MAX_PLAYER); i++) {
            let tempPlayer = new Player(playerData[i].id, playerData[i].reserved_piece, playerData[i].id_piece,playerData[i].username);
            this.players.push(tempPlayer);
        }
    }

    initGame(gameData) {
        this.status = gameData.status;
        this.playerTurn = gameData.current_player_turn;
        this.winner=gameData.winner;
    }

    //? Function to move cells in the array

    moveCell(destinationCell) {
        let currentRow = this.selectedCell.row;
        let currentCol = this.selectedCell.column;
        let destinationRow = destinationCell.row;
        let destinationCol = destinationCell.column;
        if (currentCol >= 0 && currentCol < BOARD_SIZE && currentRow >= 0 && currentRow < BOARD_SIZE) {
            this.gameboard[currentRow][currentCol] = this.selectedCell.void();
        }
        this.gameboard[destinationRow][destinationCol] = this.selectedCell.move(destinationRow, destinationCol);
        this.moveDone = true;
    }

    //? Function to push all cells of a row or column

    pushCell(destinationCell) {
        this.pushDone = true;
        let pushingDirection = this.selectedCell.getMovementDirection(destinationCell);
        let rowdir = Direction.getDirectionRow(pushingDirection);
        let columndir = Direction.getDirectionColumn(pushingDirection);
        let currentCell = destinationCell;
        let tempCell;
        this.moveCell(destinationCell);
        while (currentCell.piece != Piece.VOID) {
            let nextRow = currentCell.row + rowdir;
            let nextColumn = currentCell.column + columndir;
            if (nextRow < BOARD_SIZE && nextRow >= 0 && nextColumn < BOARD_SIZE && nextColumn >= 0) {
                tempCell = this.gameboard[nextRow][nextColumn];
                this.gameboard[nextRow][nextColumn] = currentCell.move(nextRow, nextColumn);
                currentCell = tempCell;
            }
            else {
                let player;
                switch(currentCell.piece){
                    case Piece.ROCK : 
                        this.checkWinner(currentCell,pushingDirection);
                        break;
                    case Piece.ELEPHANT :
                        player=this.getPlayerByPiece(currentCell.piece);
                        player.reservedPiece++;
                        break;
                    case Piece.RHINOCEROS :
                        player=this.getPlayerByPiece(currentCell.piece);
                        player.reservedPiece++;
                        break;
                }
                return;
            }
        }
    }

    getPlayerByPiece(piece){
        for (let i=0;i<this.players.length;i++){
            if (this.players[i].typePiece==piece) return this.players[i];
        }
    }

    getPlayerByID(id){
        for (let i=0;i<this.players.length;i++){
            if (this.players[i].id==id) return this.players[i];
        }
    }

    //? Function to check if a cell can push another one
    canPush(destinationCell) {
        let pushingDirection = this.selectedCell.getMovementDirection(destinationCell);
        if (this.selectedCell.direction != pushingDirection) return false;
        let pushStrength = 1;
        let cellToPush = destinationCell;
        while (cellToPush.piece != Piece.VOID) {
            pushStrength += cellToPush.getPushStrength(pushingDirection);
            let nextRow = cellToPush.row + Direction.getDirectionRow(pushingDirection);
            let nextColumn = cellToPush.column + Direction.getDirectionColumn(pushingDirection);
            if (nextRow >= 0 && nextRow < BOARD_SIZE && nextColumn >= 0 && nextColumn < BOARD_SIZE) {
                cellToPush = this.gameboard[cellToPush.row + Direction.getDirectionRow(pushingDirection)][cellToPush.column + Direction.getDirectionColumn(pushingDirection)];
            }
            else {
                return (pushStrength >= 0);
            }
        }

        return (pushStrength >= 0);
    }


    //? Function to change an image
    setImage(img, piece, direction) {
        switch (piece) {
            case Piece.VOID:
                img.src = '';
                break;
            case Piece.ROCK:
                img.src = 'assets/rocher.gif'
                break;
            case Piece.ELEPHANT:
                img.src = `assets/elephant${direction}.gif`
                break;
            case Piece.RHINOCEROS:
                img.src = `assets/rhinoceros${direction}.gif`
                break;
            default:
                break;
        }
    }

    //? Function to check if the requested move is possible
    movePiece(row, col) {
        if (this.moveDone == true || this.status != GameStatus.STARTED || this.playerTurn != id_player) {
            return;
        }
        let currentCell = this.gameboard[row][col];
        let player = this.getPlayer();
        if (currentCell.player != id_player && this.selectedCell == null) {
            return;
        }

        if (player.isAddingPiece) {
            if (currentCell.piece == Piece.VOID && this.canAddPiece(currentCell)) this.moveCell(currentCell);
            else if (this.canPushAddedPiece(currentCell)) this.pushCell(currentCell);
            else return;
            this.getPlayer().reservedPiece -= 1;
            this.renderBoard();
        }
        else if (this.selectedCell == null && currentCell.piece != Piece.VOID) {
            this.selectedCell = currentCell;
        }
        else if (this.selectedCell != null && currentCell.piece == Piece.VOID && this.selectedCell.canAccess(currentCell)) {
            this.moveCell(currentCell);
            this.renderBoard();
        }
        else if (this.selectedCell != null && currentCell.piece != Piece.VOID && this.selectedCell.canAccess(currentCell) && this.canPush(currentCell)) {
            this.pushCell(currentCell);
            this.renderBoard();
        }
        if (this.status==GameStatus.FINISHEDWIN || this.status==GameStatus.FINISHEDDRAW){
            this.endTurn(true);
        }
    }

    checkWinner(rockCell,pushingDirection){
        let row=rockCell.row;
        let column=rockCell.column;
        let minDistance=Infinity;
        let file=[];
        file.push(this.gameboard[row][column]);
        while (file.length!=0){
            let currentCell=file.shift();
            let tmpDist=currentCell.calcDistance(rockCell);
            if (tmpDist>minDistance){
                break;
            }
            if (currentCell.direction==pushingDirection){
                if (this.winner==null){
                    this.winner=currentCell.player;
                    this.status=GameStatus.FINISHEDWIN;
                    minDistance=tmpDist;
                }
                else if (this.winner!=currentCell.player){
                    this.status=GameStatus.FINISHEDDRAW;
                    this.winner=null;
                    break;
                }
            }
            file=addNeighbors(file,currentCell,this.gameboard);
        }
        return;
    }

    //? Function to render the board in the page
    renderBoard() {
        for (let i = 0; i < BOARD_SIZE; i++) {
            for (let j = 0; j < BOARD_SIZE; j++) {
                let img = document.getElementById(`image-${i}-${j}`);   
                this.setImage(img, this.gameboard[i][j].piece, this.gameboard[i][j].direction);
            }
        }
        let img = document.getElementById('addpiece-container');

        if (this.getPlayer()!=null && this.getPlayer().isAddingPiece) {
            this.setImage(img, this.selectedCell.piece, this.selectedCell.direction);
        }
        else {
            this.setImage(img, Piece.VOID, Direction.DOWN);
        }

        let reservedpiece=document.getElementById('reservedpiece');
        reservedpiece.innerText=this.getPlayer().reservedPiece;

        this.renderTurn();
    }

    renderTurn(){
        let turn=document.getElementById('playerturn');
        if (this.playerTurn==this.getPlayer().id){
            turn.innerText="You";
        }
        else {
            turn.innerText=this.getOtherPlayer().username;
        }
        if (this.status==GameStatus.FINISHEDDRAW){
            turn.innerText="Draw";
        }
        else if (this.status==GameStatus.FINISHEDWIN){
            turn.innerText=this.getPlayerByID(this.winner).username+" won!";
        }
    }

    getPlayer() {
        for (let i = 0; i < this.players.length; i++) {
            if (this.players[i].id == id_player) return this.players[i];
        }
        return null;
    }

    getOtherPlayer() {
        for (let i = 0; i < this.players.length; i++) {
            if (this.players[i].id != id_player) return this.players[i];
        }
    }


    canAddPiece(currentCell) {
        if (currentCell.row == 0 || currentCell.row == BOARD_SIZE - 1 || currentCell.column == 0 || currentCell.column == BOARD_SIZE - 1) {
            this.getPlayer().isAddingPiece = false;
            return true;
        }
        return false;
    }

    canPushAddedPiece(currentCell) {
        return this.selectedCell.assignTemporaryPosition(currentCell) && this.canPush(currentCell) && this.canAddPiece(currentCell);
    }

    addPiece() {
        if (this.status!=GameStatus.STARTED || this.playerTurn!=id_player || this.moveDone){
            return;
        }
        let player = this.getPlayer(id_player);
        if (player == null) return;
        if (player.reservedPiece > 0 && !player.isAddingPiece) {
            player.isAddingPiece = true;
            this.selectedCell = new Cell(player.typePiece, Direction.DOWN, id_player, undefined, undefined);
            this.renderBoard();
        }
    }

    cancel() {
        if (this.status!=GameStatus.STARTED || this.playerTurn!=id_player){
            return;
        }
        this.selectedCell = null;
        this.getPlayer().isAddingPiece = false;
        this.moveDone = false;
        this.pushDone = false;
        getGameboardData(this);
    }

    rotateSelectedPiece() {
        if (this.status!=GameStatus.STARTED || this.playerTurn!=id_player){
            return;
        }
        if (this.selectedCell != null && !this.pushDone) {
            this.selectedCell.rotate();
            this.renderBoard();
        }
    }

    endTurn(forceEnd=false) {
        if ((this.status!=GameStatus.STARTED || this.playerTurn!=id_player) && (!forceEnd)){
            return;
        }
        this.playerTurn=this.getOtherPlayer().id;
        this.moveDone = false;
        this.pushDone = false;
        this.selectedCell = null;
        sendGameboard(this);
        sendGameStatus(this);
        for (let i=0;i<this.players.length;i++){
            sendPlayerData(this.players[i]);
        }
        this.renderTurn();
    }
}

const game = new Siam();



document.addEventListener("DOMContentLoaded", () => {

    function getData(){
        getGameData(game);
        getPlayerData(game);
        getGameboardData(game);
        game.renderBoard();
    }
    getData();
    const cells = document.querySelectorAll('.cell');

    cells.forEach(cell => {
        cell.addEventListener('click', () => {
            game.movePiece(cell.getAttribute('data-row'), cell.getAttribute('data-col'));
        });
    })

    const addbutton = document.getElementById("addpiece");
    addbutton.addEventListener('click', (e) => {
        e.preventDefault();
        game.addPiece();
    })

    const cancelbutton = document.getElementById("cancel");
    cancelbutton.addEventListener('click', (e) => {
        e.preventDefault();
        game.cancel();
    })

    const rotatebutton = document.getElementById("rotate");
    rotatebutton.addEventListener('click', (e) => {
        e.preventDefault();
        game.rotateSelectedPiece();
    })

    const endbutton = document.getElementById("endturn");
    endbutton.addEventListener('click', (e) => {
        e.preventDefault();
        game.endTurn();
    })

    setInterval(function(){
        if (game.playerTurn!=id_player){
            getData();
        }
    },2000);
    
});