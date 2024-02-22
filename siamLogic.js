const BOARD_SIZE=5;
var selectedCell=null;


// DATABASE NEED TO STORE THE SAME VALUE FOR ROTATIN AND PIECE AS THE VALUES IN THIS SCRIPT
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
    constructor(piece,rotation,player,x,y){
        this.piece=piece;
        this.rotation=rotation;
        this.player=player;
        this.x=x;
        this.y=y;
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
                this.gameboard[i].push(new Cell(Piece.VOID,Rotation.UP,null,i,j));
                var tableCell=document.getElementById(`cell-${i}-${j}`);    //Create a blank img element for each cell
                tableCell.setAttribute("data-row",i);
                tableCell.setAttribute("data-col",j);
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

    /* Function to swap to cells in the array */

    moveCell(cellDest){
        this.gameboard[selectedCell.x][selectedCell.y]= new Cell(Piece.VOID,Rotation.UP,null,selectedCell.x,selectedCell.y);
        this.gameboard[cellDest.x][cellDest.y]= selectedCell;
        selectedCell.x=cellDest.x;
        selectedCell.y=cellDest.y;
    }


    /*Function to change an image*/
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
                img.classList.add("piece");
                this.setImage(img,this.gameboard[i][j].piece,this.gameboard[i][j].rotation);
            }
        }
    }
}



var jeu=new Siam();

function main(){
    jeu.init();
    jeu.renderBoard();

    const cells=document.querySelectorAll('.cell');

    cells.forEach(cell=>{
        cell.addEventListener('click',function(){
            let row=cell.getAttribute('data-row');
            let col=cell.getAttribute('data-col');
            let currentCell=jeu.gameboard[row][col];
            if (selectedCell==null && currentCell.piece!=Piece.VOID){
                selectedCell=currentCell;
            }
            else {
                jeu.moveCell(currentCell);
                jeu.renderBoard();
                selectedCell=null;
            }
        })
    })
}

document.addEventListener("DOMContentLoaded",main);