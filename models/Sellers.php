<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;

require_once "{$base_dir}includes{$ds}Database.php";
//require_once "{$base_dir}includes{$ds}Bcrypt.php";//including bycrypt for password hashing

class Seller
{
    private $db;
    private $table = 'sellers';

    public $id;
    public $name;
    public $email;
    public $password;
    public $image;
    public $address;
    public $description;

    // ✅ constructor with dependency injection
    public function __construct($database)
    {
        $this->db = $database;
    }

    // validate params
    public function validate_params($value)
    {
        return !empty(trim($value));
    }

    // check unique email
    public function check_unique_email()
    {
        $email = $this->db->escape_value($this->email);

        $sql = "SELECT id FROM {$this->table} WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($sql);
        $row = $this->db->fetch_row($result);

        return empty($row);
    }

    // register seller
    public function register_seller()
    {
        $name = $this->db->escape_value($this->name);
        $email = $this->db->escape_value($this->email);
        $password = password_hash($this->password, PASSWORD_BCRYPT);
        $image = $this->db->escape_value($this->image ?? '');
        $address = $this->db->escape_value($this->address);
        $description = $this->db->escape_value($this->description);

        $sql = "INSERT INTO {$this->table}
                (name, email, password, image, address, description)
                VALUES
                ('$name', '$email', '$password', '$image', '$address', '$description')";

        return $this->db->query($sql);
    }

    // login
    public function login()
    {
        $email = $this->db->escape_value($this->email);

        $sql = "SELECT * FROM {$this->table} WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($sql);
        $seller = $this->db->fetch_row($result);

        if (!$seller) {
            return "Seller doesn't exist.";
        }

        if (password_verify($this->password, $seller['password'])) {
            unset($seller['password']);
            return $seller;
        }

        return "Password doesn't match.";
    }

    // get all sellers
    public function all_sellers()
    {
        $sql = "SELECT id, name, image, address FROM {$this->table}";
        $result = $this->db->query($sql);
        return $this->db->fetch_array($result);
    }
}
