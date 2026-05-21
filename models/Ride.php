<?php

class Ride
{
    private $pdo;

    public function __construct($pdo)
    {
       
        $this->pdo = $pdo;
            
    }

    public function create($driver_id, $departure, $destination, $date, $departure_time, $seats, $price, $notes)
    {   
        $sql = "INSERT INTO rides (driver_id, departure, destination, date, departure_time, seats, price, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$driver_id, $departure, $destination, $date, $departure_time, $seats, $price, $notes]);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM rides WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByDriver($driver_id)
    {
        $sql = "SELECT * FROM rides WHERE driver_id = ? ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$driver_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($ride_id, $driver_id, $departure, $destination, $date, $departure_time, $seats, $price)
    {
        $sql = "UPDATE rides SET departure = ?, destination = ?, date = ?, departure_time = ?, seats = ?, price = ? WHERE id = ? AND driver_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$departure, $destination, $date, $departure_time, $seats, $price, $ride_id, $driver_id]);
    }

    public function delete($ride_id, $driver_id)
    {
        $sql = "DELETE FROM rides WHERE id = ? AND driver_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$ride_id, $driver_id]);
    }

    public function decrementSeats($ride_id, $seats)
    {
        $sql = "UPDATE rides SET seats = seats - ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$seats, $ride_id]);
    }

    public function searchAvailable($from, $to, $date , $user_id)
    {
        $sql = "
            SELECT r.*, u.name AS driver_name
            FROM rides r
            JOIN users u ON u.id = r.driver_id
            WHERE r.departure = ?
            AND r.destination = ?
            AND r.date = ?
            AND r.seats > 0
            AND r.driver_id != ?
            ORDER BY r.date ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$from, $to, $date ,$user_id]);

        $rides = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        return $rides;
    }

    // Récupérer tous les trajets avec le nom du conducteur (admin)
    public function getAll()
    {
        $sql = "
            SELECT r.id, r.departure, r.destination, r.date, r.departure_time, r.seats, r.price,
                   u.name AS driver_name
            FROM rides r
            JOIN users u ON u.id = r.driver_id
            ORDER BY r.date DESC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer un trajet par ID sans vérifier le conducteur (admin)
    public function deleteByIdAdmin($ride_id)
    {
        $sql = "DELETE FROM rides WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$ride_id]);
    }

    // Compter le nombre total de trajets (admin)
    public function countAll()
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM rides")->fetchColumn();
    }

    // Récupérer les N derniers trajets (admin dashboard)
    public function getLatest($limit = 5)
    {
        $sql = "
            SELECT r.id, r.departure, r.destination, r.date, r.departure_time, r.seats, r.price,
                   u.name AS driver_name
            FROM rides r
            JOIN users u ON u.id = r.driver_id
            ORDER BY r.date DESC
            LIMIT " . intval($limit) . "
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
