<?php
include_once __DIR__.'/../../lib/Database.class.php';

class Professor {
    private $professorID;
    private $firstName;
    private $lastName;

    private $conn;

    /**
     * Create a professor representation.
     *
     * @param $name CSV text containing professor's name.
     */
    function __construct($name) {
        $names = explode(", ", $name);
        if(count($names) > 1)
            $this->firstName = $names[1];
        else
            $this->firstName = '';
        $this->lastName = $names[0];
    }

    /**
     * Database connections for SQL queries
     */
    protected function openConn()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    protected function closeConn()
    {
        $this->conn = null;
        $database = null;
    }

    /**
     * Close database connection upon object destruction.
     */
    function __destruct() {
        $this->conn = null;
        $this->database = null;
    }

    /**
     * Synchronize with database.
     */
    function save() {
        if($this->conn == null)
            $this->openConn();

        $sql = "SELECT * FROM Professor WHERE firstName = :firstName AND lastName = :lastName LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array(':firstName' => $this->firstName, ':lastName' => $this->lastName));
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->professorID = $row['pID'];
            } else {
                $sql = "INSERT INTO Professor (firstName, lastName) VALUES (:firstName, :lastName)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array(':firstName' => $this->firstName, ':lastName' => $this->lastName));
                $this->professorID = $this->conn->lastInsertId();
            }
    }

    public function delete() {
        if($this->conn == null)
            $this->openConn();

        $sql = "DELETE FROM Professor WHERE firstName = :firstName AND lastName = :lastName";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(':firstName' => $this->firstName, ':lastName' => $this->lastName));

        if($stmt->rowCount() > 0) {
            $results = true;
        }
        else {
            $results = false;
        }

        $this->closeConn();
        return $results;
    }

    /**
     * @return Professor's internal ID.
     */
    public function getProfessorID() {
        return $this->professorID;
    }

    /**
     * @return Professor first name.
     */
    public function getFirstName() {
        return htmlspecialchars($this->firstName);
    }

    /**
     * @return Professor last name.
     */
    public function getLastName() {
        return htmlspecialchars($this->lastName);
    }
}

?>
