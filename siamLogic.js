const BOARD_SIZE=5;

//! DATABASE NEEDS TO STORE THE SAME VALUE FOR DIRECTION AND PIECE AS THE VALUES IN THIS SCRIPT
class Direction{
    static UP=0;
    static RIGHT=1;
    static DOWN=2;
    static LEFT=3;

    //? Convert a direction to the corresponding x value
    static getDirectionX(direction){
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
    static getDirectionY(direction){
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
    static VOID=0;
    static ROCK=1;
    static ELEPHANT=2;
    static RHINOCEROS=3;
}

class Cell{
    constructor(piece,direction,player,x,y){
        this.piece=piece;
        this.direction=direction;
        this.player=player;
        this.x=x;
        this.y=y;
    }


    move(x,y){
        return new Cell(this.piece,this.direction,this.player,x,y);
    }


    //? Function to create a void cell at the same position of the cell
    void(){
        return new Cell(Piece.VOID,Direction.UP,null,this.x,this.y);
    }


    //? Function to check if a cell can access another one (must be at a distance of 1 square)
    canAccess(cell){
        if (Math.abs(this.x-cell.x)==1 && this.y-cell.y==0){
            return true;
        }
        else if (Math.abs(this.y-cell.y)==1 && this.x-cell.x==0){
            return true;
        }
        return false;
    }

    //? Function to retrieve the direction of the vector to go from current cell to targeted cell
    getMovementDirection(cell){
        let difx = cell.x-this.x;
        let dify = cell.y-this.y;
        if (difx>=1) return Direction.DOWN;
        else if (difx<=-1) return Direction.UP;
        else if (dify>=1) return Direction.RIGHT;
        else if (dify<=-1) return Direction.LEFT;
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

class Siam{
    constructor(){
        this.gameboard=[];
        this.selectedCell=null;
    }

    //? Function to initialize a gameboard
    init(){
        for (let i=0;i<BOARD_SIZE;i++){
            this.gameboard.push([]);
            for (let j=0;j<BOARD_SIZE;j++){
                this.gameboard[i].push(new Cell(Piece.VOID,Direction.UP,null,i,j));
                let tableCell=document.getElementById(`cell-${i}-${j}`);    //Create a blank img element for each cell
                tableCell.setAttribute("data-row",i);
                tableCell.setAttribute("data-col",j);
                let img=document.createElement('img');
                img.src='';
                img.id=`image-${i}-${j}`;
                img.classList.add('piece');
                tableCell.appendChild(img);
            }
        }
        this.gameboard[2][1].piece=Piece.ROCK;
        this.gameboard[2][2].piece=Piece.ROCK;
        this.gameboard[2][3].piece=Piece.ROCK;
    }

    //? Function to move cells in the array

    moveCell(destinationCell){
        this.gameboard[this.selectedCell.x][this.selectedCell.y]= this.selectedCell.void();
        this.gameboard[destinationCell.x][destinationCell.y]= this.selectedCell.move(destinationCell.x,destinationCell.y);
    }

    //? Function to push all cells of a row or column

    pushCell(destinationCell){
        let pushingDirection=this.selectedCell.getMovementDirection(destinationCell);
        let xdir=Direction.getDirectionX(pushingDirection);
        let ydir=Direction.getDirectionY(pushingDirection);
        let nextCell=destinationCell;
        let tempCell;
        this.moveCell(destinationCell);
        do{
            let nextX=nextCell.x+xdir;
            let nextY=nextCell.y+ydir;
            if (nextX<BOARD_SIZE && nextX>=0 && nextY<BOARD_SIZE && nextY >=0){
                tempCell=this.gameboard[nextX][nextY];
            }
            else {
                return;
            }
            this.gameboard[nextX][nextY]=nextCell.move(nextX,nextY);
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
            let nextX=cellToPush.x+Direction.getDirectionX(pushingDirection);
            let nextY=cellToPush.y+Direction.getDirectionY(pushingDirection);
            if (nextX>=0 && nextX<BOARD_SIZE && nextY>=0 && nextY<BOARD_SIZE){
                cellToPush=this.gameboard[cellToPush.x+Direction.getDirectionX(pushingDirection)][cellToPush.y+Direction.getDirectionY(pushingDirection)];
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
    tryMovePiece(row,col){
        let currentCell=this.gameboard[row][col];
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

let game=new Siam();

document.addEventListener("DOMContentLoaded", () => {
    game.init();
    game.renderBoard();

    const cells=document.querySelectorAll('.cell');

    cells.forEach(cell=>{
        cell.addEventListener('click',() => {
            game.tryMovePiece(cell.getAttribute('data-row'),cell.getAttribute('data-col'));
        });
    })
});