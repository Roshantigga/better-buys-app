<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;

require_once "{$base_dir}includes{$ds}Database.php";

class Product {

    private $db;
    private $table = 'products';

    public $id;
    public $seller_id;
    public $name;
    public $image;
    public $price_per_kg;
    public $description;
    public $interaction_count;

    // Constructor
    public function __construct($database){
        $this->db = $database;
    }

    // Validate parameters
    public function validate_params($value){
        return (!empty($value));
    }


    ///stroring product details
    public function add_product() {

        $this->seller_id = htmlspecialchars(strip_tags(trim($this->seller_id)));
        $this->name = htmlspecialchars(strip_tags(trim($this->name)));
        $this->image = htmlspecialchars(strip_tags(trim($this->image)));
        $this->price_per_kg = htmlspecialchars(strip_tags(trim($this->price_per_kg)));
        $this->description = htmlspecialchars(strip_tags(trim($this->description)));

        $sql = "INSERT INTO {$this->table} 
                (seller_id, name, image, price_per_kg, description) 
                VALUES (
                '".$this->db->escape_value($this->seller_id)."',
                '".$this->db->escape_value($this->name)."',
                '".$this->db->escape_value($this->image)."',
                '".$this->db->escape_value($this->price_per_kg)."',
                '".$this->db->escape_value($this->description)."'
                )";

        $result = $this->db->query($sql);

        return $result ? true : false;
    }

    //method to return the list of product per seller

    public function get_products_by_seller($seller_id) {
        $seller_id = $this->db->escape_value($seller_id);

        $sql = "SELECT id, seller_id, name, image, price_per_kg, description, interaction_count 
                FROM {$this->table} 
                WHERE seller_id = '$seller_id'";

        $result = $this->db->query($sql);

        return $this->db->fetch_array($result);
    }
}
//class end

//object
//$product = new Product();
