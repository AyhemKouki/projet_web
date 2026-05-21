<?php


class Booking
{
    // =========================
    // DATABASE CONNECTION
    // =========================
    private $pdo;

    // =========================
    // BOOKING PROPERTIES
    // =========================
    public $id;
    public $user_id;
    public $ride_id;
    public $seats;
    public $created_at;


    // =========================
    // CONSTRUCTOR
    // =========================
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    // =========================
    // CHECK IF BOOKING EXISTS
    // (User already booked this ride?)
    // =========================
    public function exists($user_id, $ride_id)
    {
        $sql = "
            SELECT id
            FROM bookings
            WHERE user_id = ?
            AND ride_id = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $ride_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // =========================
    // CREATE NEW BOOKING
    // =========================
    public function create($user_id, $ride_id, $seats)
    {
        $sql = "
            INSERT INTO bookings (user_id, ride_id, seats)
            VALUES (?, ?, ?)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $user_id,
            $ride_id,
            $seats
        ]);
    }


    // =========================
    // GET ALL BOOKINGS OF USER
    // =========================
    public function getUserBookings($user_id)
    {
        $sql = "
            SELECT
                b.id AS booking_id,
                b.created_at AS booking_date,
                COALESCE(b.seats, 1) AS booked_seats,

                a.rating AS booking_rating,

                r.id AS ride_id,
                r.departure,
                r.destination,
                r.date,
                r.departure_time,
                r.price,

                u.name AS driver_name,
                u.email AS driver_email

            FROM bookings b

            JOIN rides r ON b.ride_id = r.id
            JOIN users u ON u.id = r.driver_id

            LEFT JOIN avis a
                ON a.driver_id = r.driver_id
               AND a.user_id = ?

            WHERE b.user_id = ?

            ORDER BY r.date DESC, b.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id, $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDriverBookings($driver_id)
    {
        $sql = "
            SELECT
                r.id as ride_id,
                r.departure,
                r.destination,
                r.date,
                r.price,
                r.seats,
                b.id as booking_id,
                u.name as passenger_name,
                u.email as passenger_email,
                b.created_at as booking_date
            FROM rides r
            JOIN bookings b ON b.ride_id = r.id
            JOIN users u ON u.id = b.user_id
            WHERE r.driver_id = ?
            ORDER BY r.date DESC, b.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$driver_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =========================
    // CANCEL BOOKING
    // =========================
    public function cancel($bookingId, $userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, ride_id, seats as booked_seats
            FROM bookings
            WHERE id = ? AND user_id = ?
        ");

        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->pdo->prepare("
            DELETE FROM bookings
            WHERE id = ? AND user_id = ?
        ")->execute([$bookingId, $userId]);

        $this->pdo->prepare("
            UPDATE rides
            SET seats = seats + ?
            WHERE id = ?
        ")->execute([$booking['booked_seats'], $booking['ride_id']]);

        return [
            'success' => true,
            'message' => "Réservation annulée avec succès."
        ];
    }


    // =========================
    // RATE A DRIVER (FROM BOOKING)
    // =========================
    public function rate($bookingId, $rating, $userId)
    {
        // STEP 1: Get booking + driver
        $sql = "
            SELECT b.id, r.driver_id
            FROM bookings b
            JOIN rides r ON b.ride_id = r.id
            WHERE b.id = ? AND b.user_id = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$bookingId, $userId]);

        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // STEP 2: If booking not found → error
        if (!$booking) {
            throw new Exception("Booking introuvable.");
        }

        // STEP 3: Save driver id
        $driverId = $booking['driver_id'];

        // STEP 4: Avis object
        $avis = new Avis($this->pdo);

        // STEP 5: Insert or update rating
        if ($avis->exists($userId, $driverId)) {
            $avis->update($userId, $driverId, $rating);
        } else {
            $avis->create($userId, $driverId, $rating);
        }

       
        return [
            'success' => true,
            'message' => "Merci pour votre note !"
        ];
    }

    // Compter le nombre total de réservations (admin)
    public function countAll()
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    }
}