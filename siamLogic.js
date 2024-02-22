const BOARD_SIZE=5;

class Rotation{
    static UP=0;
    static RIGHT=1;
    static DOWN=2;
    static LEFT=3;
}

class Piece {
    static VOID=0;
    static ROCK=1;
    static ELEPHANT=2;
    static RHINOCEROS=3;
}

class Cell{
    constructor(piece,rotation,player){
        this.piece=piece;
        this.rotation=rotation;
        this.player=player;
    }
}

class Siam{
    constructor(){
        this.gameboard=[];
    }

    init(){
        for (let i=0;i<BOARD_SIZE;i++){
            this.gameboard.push([]);
            for (let j=0;j<BOARD_SIZE;j++){
                this.gameboard[i].push(new Cell(Piece.VOID,Rotation.UP,null));
            }
        }
        this.gameboard[2][1].piece=Piece.ROCK;
        this.gameboard[2][2].piece=Piece.ROCK;
        this.gameboard[2][3].piece=Piece.ROCK;
    }

    renderBoard(){
        for (let i=0;i<BOARD_SIZE;i++){
            for (let j=0;j<BOARD_SIZE;j++){
                tableCell=document.getElementById(`cell-${i}-${j}`);
                tableCell.textContent=`${this.plateau[i][j].piece}`;
            }
        }
    }
}

function main(){
    jeu=new Siam();
    jeu.init();
    jeu.renderBoard();
}

document.addEventListener("DOMContentLoaded",main);