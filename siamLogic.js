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

    /* Function to initialize a gameboard */
    init(){
        for (let i=0;i<BOARD_SIZE;i++){
            this.gameboard.push([]);
            for (let j=0;j<BOARD_SIZE;j++){
                this.gameboard[i].push(new Cell(Piece.VOID,Rotation.UP,null));
                var tableCell=document.getElementById(`cell-${i}-${j}`);    //Create a blank img element for each cell
                var img= document.createElement('img');
                img.src='';
                img.id=`image-${i}-${j}`;
                tableCell.appendChild(img);
            }
        }
        this.gameboard[2][1].piece=Piece.ROCK;
        this.gameboard[2][2].piece=Piece.ROCK;
        this.gameboard[2][3].piece=Piece.ROCK;
    }

    /*Functio to change an image*/

    setImage(img,piece,rotation){
        switch (piece) {
            case Piece.VOID:
                img.src='';
                break;
            case Piece.ROCK:
                img.src='assets/rocher.gif'
                break;
            case Piece.ELEPHANT:
                img.src=`assets/elephant${rotation}.gif`
                break;
            case Piece.RHINOCEROS:
                img.src=`assets/rhinoceros${rotation}.gif`
                break;
            default:
                break;
        }
    }


    /*Function to change the board in the page*/
    renderBoard(){
        for (let i=0;i<BOARD_SIZE;i++){
            for (let j=0;j<BOARD_SIZE;j++){
                var img = document.getElementById( `image-${i}-${j}`)
                this.setImage(img,this.gameboard[i][j].piece,this.gameboard[i][j].rotation);
            }
        }
    }
}



var jeu=new Siam();

function main(){
    jeu.init();
    jeu.renderBoard();
}

document.addEventListener("DOMContentLoaded",main);