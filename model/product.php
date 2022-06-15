<?php
class Product {
    //properties
    public $productID;
    public $title;
    // Methods
    function setProductID($productID_) {
        $this->productID = $productID_;
    }
    function getProductID() {
        return $this->productID;
    }
    function setTitle($title_) {
        $this->title = $title_;
    }
    function getTitle() {
        return $this->title;
    }
}