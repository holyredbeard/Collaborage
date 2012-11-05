<?php

namespace Model;

require_once ('Model/StoreListHandler.php');

class Database {
    private $mysqli = NULL;

    /**
    * Ansluter till databasen.
    * 
    * @param DBConfig $config
    * @return Databas-objekt
    */
    public function Connect(DBConfig $config) {
            $this->mysqli = new \mysqli(
            $config->m_host,
            $config->m_user,
            $config->m_pass,
            $config->m_db
        );

        if ($this->mysqli->connect_error) {
            throw new Exception($this->mysqli->connect_error);
        }

        $this->mysqli->set_charset('utf8');

        return true;
    }

    /**
    * Preparerar queryn.
    * 
    * @param $sql String Sql query
    * @return mysqli_stmt 
    */
    public function Prepare($query) {
        $ret = $this->mysqli->prepare($query);
        
        if ($ret == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        return $ret;      
    }

    /**
    * Körs när användare registreras för att lägga till användaren i databasen.
    * 
    * @param $stmt mysqli_stmt
    * @return boolean
    */
    public function RunInsertQuery(\mysqli_stmt $stmt) {
                    
        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        if ($stmt->insert_id) {
            return true;
        }

        return false;
    }

    /**
    * Kontrollerar om användarnamnet finns registrerat sedan tidigare.
    * 
    * @param $stmt mysqli_stmt
    * @return boolean
    */
    public function CheckUser($stmt) {

        if ($stmt->execute() == false) {
            throw new \Exception($this->mysqli->error);
        }
        
        //Binder parametrarna så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($userId, $username, $password, $isAdmin) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        // Hämtar datan och lägger i arrayen $user
        if ($stmt->fetch()) {

            $user = array('userId' => $userId,
                          'username' => $username,
                          'isAdmin' => $isAdmin);

            return $user;
        } else {
            return null;       // Ingen match i databasen
        }
    }

    /**
    * Tar bort en eller flera användare.
    * 
    * @param \mysqli_stmt $stmt
    */
    public function DeleteUsers(\mysqli_stmt $stmt) {

        if ($stmt === FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
            return false;
        }
        else {
            return true;
        }
    }

    /**
    * Hämtar samtliga användare i databasen.
    * 
    * @param \mysqli_stmt $stmt
    * @return Array med användare (ids, användarnamn)
    */
    public function GetUsers(\mysqli_stmt $stmt) {
        $userArray = array(
            0 => array(),
            1 => array()
        );
        
        if ($stmt === FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        if ($stmt->execute() == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
            
        // Binder parametrarna så att vi kan få datan från dem via fetch.
        if ($stmt->bind_result($field1, $field2, $field3, $field4) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Hämtar ids och användarnamn och lägger i arrayen.
        while ($stmt->fetch()) {
            array_push($userArray[0], $field1);
            array_push($userArray[1], $field2);
        }
        
        $stmt->Close();
        
        return $userArray;
    }

    /**
    * Kontrollerar om en användare har administratörs-behörighet.
    * 
    * @param $stmt mysqli_stmt
    * @return boolean $isAdmin
    */
    public function CheckIfAdmin($stmt) {

        if ($stmt->execute() == false) {
            throw new \Exception($this->mysqli->error);
        }
        
        //Binder parametrarna så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($isAdmin) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        // Hämtar $isAdmin från db
        if ($stmt->fetch()) {
            return $isAdmin;
        }
        else {
            return null;       // Ingen match i databasen
        }

    }

    /*
        Funktioner för listor
    */
    
    /**
    * Sparar en ny lista i db
    * 
    * @param $stmt mysqli_stmt
    * @return Int listid
    */
    public function CreateNewList($stmt) {

        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        // Returnerar listId
        return $stmt->insert_id;
    }

    /**
    * Sparar ner listobjekt i db
    * 
    * @param $stmt mysqli_stmt
    */
    public function InsertListObjects(\mysqli_stmt $stmt, $list) {
               
        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        //Binder parametrarna så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($listElemName, $listId, $listElemDesc) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Sätter listobjekten i list-klassen via SetListObjects.
        if ($stmt->fetch()) {
            $list->SetListObjects($listElemName, $listId, $listElemDesc);
        }
    }

    /**
    * Hämtar listor från db
    * 
    * @param $stmt mysqli_stmt
    * @return \Model\StoreListHandler() object
    */
    public function GetLists($stmt) {

        $lists = new \Model\StoreListHandler();

        if ($stmt === FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        if ($stmt->execute() == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
            
        //Binder parametrarna så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($listId, $userId, $listName, $creationDate, $listIsDone) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Sätter listinformationen i list-klassen via SetList.
        while ($stmt->fetch()) {
            $lists->SetLists($listId, $userId, $listName, $creationDate, $listIsDone);
        }

        $stmt->Close();
        
        return $lists;
    }

    /**
    * 
    * @param $stmt mysqli_stmt
    * @return \Model\StoreListHandler() object
    */
    public function HasFinishedSorting($stmt) {

        if ($stmt->execute() == false) {
            throw new \Exception($this->mysqli->error);
        }
        
        //Binder parametern så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($isFinished) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        // Hämtar och returnerar $isFinished
        if ($stmt->fetch()) {
            return $isFinished;
        }
        else {
            return null;       // Ingen match i databasen
        }
    }

    /**
    * Kontrollerar om det finns någon användare som inte har sorterat,
    * och returnerar i så fall false (annars true).
    * 
    * @param $stmt mysqli_stmt
    * @return boolean 
    */
    public function AllHasSorted($stmt) {

        $allHasSorted = true;

        if ($stmt->execute() == false) {
            throw new \Exception($this->mysqli->error);
        }
        
        //Binder parametern så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($result) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        while ($stmt->fetch()) {
            echo $result;
            if ($result == 0) {
                $allHasSorted = false;
            }
        }

        return $allHasSorted;
    }

    /**
    * Kontrollerar om en lista är sorterad
    * 
    * @param $stmt mysqli_stmt
    * @return boolean 
    */
    public function CheckListStatus($stmt) {

        if ($stmt === FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
            
        //Binder parametern så att vi kan få datan från dem via fetch
        if ($stmt->bind_result($listIsDone) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Returerar true om listan är sorterad
        if ($stmt->fetch()) {
            return $listIsDone;
        } else {
            return null;
        }
    }

    /**
    * Hämtar användare som knutna till en lista
    * 
    * @param $stmt mysqli_stmt
    * @return boolean 
    */
    public function GetListUsersIds($stmt) {

        if ($stmt === FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        //execute the statement
        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
            
        //Bind the $ret parameter so when we call fetch it gets its value
        if ($stmt->bind_result($userId) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Hämtar ids och användarnamn och lägger i arrayen.
        while ($stmt->fetch()) {
            $listUsers[] = $userId;
        }
        
        $stmt->Close();
        
        return $listUsers;
    }

    public function GetListOrders($stmt) {

        if ($stmt === FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        //execute the statement
        if ($stmt->execute() == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
            
        //Bind the $ret parameter so when we call fetch it gets its value
        if ($stmt->bind_result($listElemId, $listElemPoints) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Hämtar ids och användarnamn och lägger i arrayen.
        while ($stmt->fetch()) {
            if ($listElemId != null) {
                $listOrders[] = array('listElemId' => $listElemId,
                                      'listElemPoints' => $listElemPoints);
            }
        }

        $stmt->Close();

        return $listOrders;
    }

    public function GetListOptions(\mysqli_stmt $stmt) {
        
        if ($stmt === FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        //execute the statement
        if ($stmt->execute() == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
            
        //Bind the $ret parameter so when we call fetch it gets its value
        if ($stmt->bind_result($listId, $userId, $listName, $creationDate, $listCreator) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }
        
        // Hämtar ids och användarnamn och lägger i arrayen.
        while ($stmt->fetch()) {
            $listOptions = array('listId' => $listId,
                                 'userId' => $userId,
                                 'listName' => $listName,
                                 'creationDate' => $creationDate,
                                 'listCreator' => $listCreator);
        }
        
        $stmt->Close();
        
        return $listOptions;
    }

    public function GetListElements(\mysqli_stmt $stmt) {

        if ($stmt === FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        //execute the statement
        if ($stmt->execute() == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
            
        //Bind the $ret parameter so when we call fetch it gets its value
        if ($stmt->bind_result($listElemId, $listElemName, $listElemDesc, $listElemOrderPlace) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        $listElements = array();
        
        // Hämtar ids och användarnamn och lägger i arrayen.
        while ($stmt->fetch()) {
            $listElements[] = array('listElemId' => $listElemId,
                                 'listElemName' => $listElemName,
                                 'listElemDesc' => $listElemDesc,
                                 'listElemOrderPlace' => $listElemOrderPlace);
        }
        
        $stmt->Close();

        return $listElements;
    }

    public function GetListUsers(\mysqli_stmt $stmt) {

        if ($stmt === FALSE) {
                throw new \Exception($this->mysqli->error);
        }
        
        //execute the statement
        if ($stmt->execute() == FALSE) {
                throw new \Exception($this->mysqli->error);
        }
            
        //Bind the $ret parameter so when we call fetch it gets its value
        if ($stmt->bind_result($userId, $username, $hasStarted, $isFinished) == FALSE) {
            throw new \Exception($this->mysqli->error);
        }

        $listUsers = array();

        while ($stmt->fetch()) {
            $listUsers[] = array('userId' => $userId,
                                 'username' => $username,
                                 'hasStarted' => $hasStarted,
                                 'isFinished' => $isFinished);
        }

        return $listUsers;
    }

    /**
    * Körs för att stänga databasen
    */
    public function Close() {
            return $this->mysqli->close();
    }


    /**
    * Kedje-tester för applikationen
    *
    * @return boolean
    */
    public static function test() {

        $db = new Database();

        /**
        * Test 1: Testa så att man kan ansluta till databasen.
        */

        if ($db->Connect(new DBConfig) == FALSE) {
            echo "Database - Test 1: Connect(), misslyckades (det gick att ansluta).";
            return false;
        }


        /**
        * Test 2: Testa så att man Prepare() fungerar.
        */

        $query = "INSERT INTO user (username, password) VALUES (?, ?)";

        if ($db->Prepare($query) == FALSE) {
            echo "Database - Test 2: DoLogin(), misslyckades (det gick att ansluta).";
            return false;
        }


        /**
        * Test 3: Testa så att man kan lägga till en användare.
        */

        $query = "INSERT INTO user (username, password) VALUES ('testuser05', '123456')";
        $stmt = $db->Prepare($query);
        
        if ($db->RunInsertQuery($stmt) == FALSE) {
            echo "Database - Test 3: RunInsertQuery(), misslyckades (det gick att lägga till en användare).";
            return false;
        }


        /**
        * Test 4: Kolla så att användaren kan hittas.
        */

        $query = "SELECT * FROM user WHERE username='testuser05' AND password='123456'"; 
        $stmt = $db->Prepare($query);

        if ($db->CheckUser($stmt) == FALSE) {
            echo "Database - Test 4: CheckUser(), misslyckades (användaren kunde inte hittas).";
            return false;
        }

        $db->Connect(new DBConfig);


        /**
        * Test 5: Kolla så att det går att ta bort en användare.
        */

        $query = "DELETE FROM user WHERE username ='testuser05'"; 
        $stmt = $db->Prepare($query);

        if ($db->DeleteUsers($stmt) == FALSE) {
            echo "Database - Test 5: DeleteUsers(), misslyckades (det gick inte att ta bort användaren).";
            return false;
        }


        /**
        * Test 6 och 7: Kolla så att användarna kan hämtas.
        */

        $query = "SELECT * FROM user";
        $stmt = $db->Prepare($query);
        
        $userArray = $db->GetUsers($stmt);

        if (is_array($userArray) == FALSE) {
            echo "Database - Test 6: GetUsers(), misslyckades (ingen array hämtades med användare).";
            return false;
        }
        else if (count($userArray) != 2){
            echo "Database - Test 7: GetUsers(), misslyckades (arrayen som hämtades innehåller ej de två arrayerna för id och användarnamn).";
            return false;            
        }


        /**
        * Test 8: Kolla så att det går att stänga databasen.
        */
        if ($db->Close() == FALSE) {
            echo "Database - Test 8: Close(), misslyckades (det gick att stänga databasen).";
            return false;
        }
        
        return true;
    }  
}