<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . '..') . $ds;

require_once "{$base_dir}includes{$ds}Database.php";

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

    // Constructor (Dependency Injection)
    public function __construct($database)
    {
        $this->db = $database;
    }

    // Validate parameters
    public function validate_params($value)
    {
        return isset($value) && trim($value) !== '';
    }

    // Check if email is unique
    public function check_unique_email()
    {
        $email = $this->db->escape_value($this->email);

        $sql = "SELECT id FROM {$this->table} WHERE email = '$email' LIMIT 1";
        $result = $this->db->query($sql);
        $row = $this->db->fetch_row($result);

        return empty($row);
    }

    // Register seller (bcrypt hashing)
    public function register_seller()
    {
        $name = $this->db->escape_value($this->name);
        $email = $this->db->escape_value($this->email);

        // ✅ bcrypt hashing
        $password = password_hash($this->password, PASSWORD_BCRYPT, [
            'cost' => 12
        ]);

        $image = $this->db->escape_value($this->image ?? '');
        $address = $this->db->escape_value($this->address);
        $description = $this->db->escape_value($this->description);

        $sql = "INSERT INTO {$this->table}
                (name, email, password, image, address, description)
                VALUES
                ('$name', '$email', '$password', '$image', '$address', '$description')";

        return $this->db->query($sql);
    }

    // Login seller
    public function login()
    {
        $email = $this->db->escape_value($this->email);

        // 🔥 IMPORTANT FIX: alias PASSWORD → password
        $sql = "SELECT 
                    id,
                    name,
                    email,
                    PASSWORD AS password,
                    image,
                    address,
                    description
                FROM {$this->table}
                WHERE email = '$email'
                LIMIT 1";

        $result = $this->db->query($sql);
        $seller = $this->db->fetch_row($result);

        if (!$seller) {
            return "Seller doesn't exist.";
        }

        // ✅ bcrypt verification
        if (password_verify($this->password, $seller['password'])) {
            unset($seller['password']);
            return $seller;
        }

        return "Password doesn't match.";
    }

    // Get all sellers
    public function all_sellers()
    {
        $sql = "SELECT id, name, image, address FROM {$this->table}";
        $result = $this->db->query($sql);
        return $this->db->fetch_array($result);
    }
}
