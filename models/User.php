<?php

class User
{
    public $id;
    public $nom;
    public $email;
    public $phone;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($name, $email, $password, $phone)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword, $phone]);
    }

    // Récupérer tous les utilisateurs (admin)
    public function getAll()
    {
        $sql = "SELECT id, name, email, phone FROM users ORDER BY name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un utilisateur par ID (admin)
    public function deleteById($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Compter le nombre total d'utilisateurs (admin)
    public function countAll()
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    // Mettre à jour le profil de l'utilisateur
    public function updateProfile($id, $name, $email, $phone, $password = null)
    {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $email, $phone, $hashedPassword, $id]);
        } else {
            $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$name, $email, $phone, $id]);
        }
    }

}
