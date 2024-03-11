const BOARD_SIZE=5;
const id_player=parseInt(document.getElementById("id_player").value);
//? Ajax functions

function getGameboard(callback){
    $.ajax({
        url:'sendGameboard.php',
        type:"GET",
        dataType:"json",
        success: function(data){
            game.init(data);
        },
        error:function(xhr,status,error){
            console.error(error);
        }
    })
}

function sendGameboardCell(cell){
    $.ajax({
        url:'updateGameboardCell.php',
        type:"POST",
        data:{
            row:cell.row,
            column:cell.column,
            id_piece:cell.piece,
            id_player:cell.player,
            direction:cell.direction
        },
        success: function(){
            console.log("youhou");
        },
        error : function(xhr,status,error){
            console.error(error);
        }

    })
}

//! DATABASE NEEDS TO STORE THE SAME VALUE FOR DIRECTION AND PIECE AS THE VALUES IN THIS SCRIPT
class Direction{
    static UP=0;
    static RIGHT=1;
    static DOWN=2;
    static LEFT=3;

    //? Convert a direction to the corresponding x value
    static getDirectionRow(direction){
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
    static getDirectionColumn(direction){
        switch(direction){
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
    static VOID=null;
    static ROCK=1;
    static ELEPHANT=2;
    static RHINOCEROS=3;
}

class Cell{
    constructor(piece,direction,player,row,column){
        this.piece=piece;
        this.direction=direction;
        this.player=player;
        this.row=row;
        this.column=column;
    }


    move(row,column){
        return new Cell(this.piece,this.direction,this.player,row,column);
    }


    //? Function to create a void cell at the same position of the cell
    void(){
        return new Cell(Piece.VOID,null,null,this.row,this.column);
    }


    //? Function to check if a cell can access another one (must be at a distance of 1 square)
    canAccess(cell){
        if (Math.abs(this.row-cell.row)==1 && this.column-cell.column==0){
            return true;
        }
        else if (Math.abs(this.column-cell.column)==1 && this.row-cell.row==0){
            return true;
        }
        return false;
    }

    //? Function to retrieve the direction of the vector to go from current cell to targeted cell
    getMovementDirection(cell){
        let difrow = cell.row-this.row;
        let difcolumn = cell.column-this.column;
        if (difrow>=1) return Direction.DOWN;
        else if (difrow<=-1) return Direction.UP;
        else if (difcolumn>=1) return Direction.RIGHT;
        else if (difcolumn<=-1) return Direction.LEFT;
    }

    //? Function to get the push strength depending on the piece direction and the direction it is pushed
    getPushStrength(directionPush){
        let pushFactor=Math.abs(directionPush-this.direction); // If the push factor is equal to 2 then the pieces are opposite, if it's equal to 0 then it is the same piece, otherwise the piece is anything else
        if (this.piece==Piece.ROCK){
            return 0;
        }
        if (pushFactor==2) {
            return -1;
        }
        else if (pushFactor==0){
            return 1;
        }
        return 0;
    }

}

class Player{
    constructor(){
        this.id=null;
        this.reservedPiece=0;
    }
}

class Siam{
    constructor(){
        this.gameboard=[];
        this.selectedCell=null;
        this.players=[];
    }

    //? Function to initialize a gameboard
    init(gameboardData){
        for (let i=0;i<BOARD_SIZE;i++){
            this.gameboard.push([]);
            for (let j=0;j<BOARD_SIZE;j++){
                let gameboardCell=gameboardData[i*BOARD_SIZE+j];
                this.gameboard[i][j]=new Cell(gameboardCell.id_piece,gameboardCell.direction,gameboardCell.id_player,gameboardCell.row,gameboardCell.column);
            }
        }
        this.renderBoard();
    }

    //? Function to move cells in the array

    moveCell(destinationCell){
        let currentRow=this.selectedCell.row;
        let currentCol=this.selectedCell.column;
        let destinationRow=destinationCell.row;
        let destinationCol=destinationCell.column;
        this.gameboard[currentRow][currentCol]= this.selectedCell.void();
        this.gameboard[destinationRow][destinationCol]= this.selectedCell.move(destinationRow,destinationCol);
        sendGameboardCell(this.gameboard[currentRow][currentCol]);
        sendGameboardCell(this.gameboard[destinationRow][destinationCol]);
    }

    //? Function to push all cells of a row or column

    pushCell(destinationCell){
        let pushingDirection=this.selectedCell.getMovementDirection(destinationCell);
        let rowdir=Direction.getDirectionRow(pushingDirection);
        let columndir=Direction.getDirectionColumn(pushingDirection);
        let nextCell=destinationCell;
        let tempCell;
        this.moveCell(destinationCell);
        do{
            let nextRow=nextCell.row+rowdir;
            let nextColumn=nextCell.column+columndir;
            if (nextRow<BOARD_SIZE && nextRow>=0 && nextColumn<BOARD_SIZE && nextColumn >=0){
                tempCell=this.gameboard[nextRow][nextColumn];
            }
            else {
                return;
            }
            this.gameboard[nextRow][nextColumn]=nextCell.move(nextRow,nextColumn);
            sendGameboardCell(this.gameboard[nextRow][nextColumn]);
            nextCell=tempCell;
        }while(nextCell.piece!=Piece.VOID);
    }

    //? Function to check if a cell can push another one
    canPush(destinationCell){
        let pushingDirection=this.selectedCell.getMovementDirection(destinationCell);
        if (this.selectedCell.direction!=pushingDirection) return false;
        let pushStrength=1;
        let cellToPush=destinationCell;
        while (cellToPush.piece!=Piece.VOID){
            pushStrength+=cellToPush.getPushStrength(pushingDirection);
            let nextRow=cellToPush.x+Direction.getDirectionRow(pushingDirection);
            let nextColumn=cellToPush.y+Direction.getDirectionColumn(pushingDirection);
            if (nextRow>=0 && nextRow<BOARD_SIZE && nextColumn>=0 && nextColumn<BOARD_SIZE){
                cellToPush=this.gameboard[cellToPush.row+Direction.getDirectionRow(pushingDirection)][cellToPush.column+Direction.getDirectionColumn(pushingDirection)];
            }
            else {
                return (pushStrength>=1);
            }
        }

        return (pushStrength>=1);
    }


    //? Function to change an image
    setImage(img,piece,direction){
        switch (piece) {
            case Piece.VOID:
                img.src='';
                break;
            case Piece.ROCK:
                img.src='assets/rocher.gif'
                break;
            case Piece.ELEPHANT:
                img.src=`assets/elephant${direction}.gif`
                break;
            case Piece.RHINOCEROS:
                img.src=`assets/rhinoceros${direction}.gif`
                break;
            default:
                break;
        }
    }

    //? Function to check if the requested move is possible
    movePiece(row,col){
        let currentCell=this.gameboard[row][col];
        if (currentCell.player!=id_player && this.selectedCell==null){
            return;
        }
        if (this.selectedCell==null && currentCell.piece!=Piece.VOID){
            this.selectedCell=currentCell;
        }
        else if (this.selectedCell!=null && currentCell.piece==Piece.VOID && this.selectedCell.canAccess(currentCell)){
            this.moveCell(currentCell);
            this.renderBoard();
            this.selectedCell=null;
        }
        else if (this.selectedCell!=null && currentCell.piece!=Piece.VOID && this.selectedCell.canAccess(currentCell) && this.canPush(currentCell)){
            this.pushCell(currentCell);
            this.renderBoard();
            this.selectedCell=null;
        }
    }
    

    //? Function to render the board in the page
    renderBoard(){
        for (let i=0;i<BOARD_SIZE;i++){
            for (let j=0;j<BOARD_SIZE;j++){
                let img = document.getElementById(`image-${i}-${j}`);
                this.setImage(img,this.gameboard[i][j].piece,this.gameboard[i][j].direction);
            }
        }
    }
}

const game=new Siam();

document.addEventListener("DOMContentLoaded", () => {

    getGameboard();

    const cells=document.querySelectorAll('.cell');

    cells.forEach(cell=>{
        cell.addEventListener('click',() => {
            game.movePiece(cell.getAttribute('data-row'),cell.getAttribute('data-col'));
        });
    })
});