<?php

class Avis
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ajouter un avis
    public function create($user_id, $driver_id, $rating) {
        $stmt = $this->pdo->prepare("
            INSERT INTO avis (user_id, driver_id, rating, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $driver_id, $rating]);
    }

    // Vérifier si déjà noté
    public function exists($user_id, $driver_id) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM avis WHERE user_id = ? AND driver_id = ?
        ");
        $stmt->execute([$user_id, $driver_id]);
        return $stmt->rowCount() > 0;
    }

    // Moyenne des notes d’un driver
    public function getAverage($driver_id) {
        $stmt = $this->pdo->prepare("
            SELECT AVG(rating) as avg_rating, COUNT(*) as total
            FROM avis
            WHERE driver_id = ?
        ");
        $stmt->execute([$driver_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un avis existant
    public function update($user_id, $driver_id, $rating) {
        $stmt = $this->pdo->prepare("UPDATE avis SET rating = ?, created_at = NOW() WHERE user_id = ? AND driver_id = ?");
        return $stmt->execute([$rating, $user_id, $driver_id]);
    }
}