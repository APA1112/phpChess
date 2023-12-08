<?php

abstract class Piece {
    const WHITE = 0;
    const BLACK = 1;

    protected $color;
    protected $board;

    public function __construct($color, $board) {
        $this->color = $color;
        $this->board = $board;
    }

    public function getColor() {
        return $this->color;
    }

    public function getBoard() {
        return $this->board;
    }

    abstract public function canMove($fromRow, $fromCol, $toRow, $toCol);

    abstract public function countMoves($fromRow, $fromCol);
}