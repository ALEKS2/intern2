<?php
/**
 * The login clas
 */
abstract class Login{
    var $password;

    /**
     * function for logging in
     * @param resource(db connection)
     * @return string | array
     */
    abstract function userLogin($db);
}
/**
 * Academic supervisor login class
 */
class AccademicLogin extends Login{
    var $username;
    function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }
    function userLogin($db){
        $sql ='SELECT * FROM accademic_supervisor WHERE username = :username AND password = :password';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = $stmt->rowCount();
        if($rows == 1){
            return $result[0];
        }else{
            return "failed";
        }
    }
}
/**
 * field supervisor login class
 */
class FieldLogin extends Login{
    function __construct($password){
        $this->password = $password;
    }

    function userLogin($db){
        $sql = 'SELECT * FROM field_supervisor WHERE password = :password AND status = :status';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindValue(':status', 'approved');
        $stmt->execute();
        $rows = $stmt->rowCount();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($rows == 1){
            return $result[0];
        }else{
            return "failed";
        }
    }
}

class AdminLogin extends Login{
    var $username;
    function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    function userLogin($db){
        $sql ='SELECT * FROM admin WHERE username = :username AND password = :password';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = $stmt->rowCount();
        if($rows == 1){
            return $result[0];
        }else{
            return "failed";
        }
    }
}
/**
 * The field supervisor class
 */
class FieldSupervisor{
    var $name;
    var $email;
    var $id_number;
    var $position;
    var $organization;
    var $org_website;
    var $password;
    var $status;
    var $image_tmp_name;
    var $image_destination;
/**
 * field supervisor constructor
 */
    function __construct($name, $email, $id_number, $position, $organization, $org_website, $password, $status, $image_tmp_name, $image_destination){
        $this->name = $name;
        $this->email = $email;
        $this->id_number = $id_number;
        $this->position = $position;
        $this->organization = $organization;
        $this->org_website = $org_website;
        $this->password = $password;
        $this->status = $status;
        $this->image_tmp_name = $image_tmp_name;
        $this->image_destination = $image_destination;
    }
/**
 * function for inserting new field supervisor
 * @param resource(db connection),
 * @param string(profile image place holder)
 */
    function insertFieldSupervisor($db, $profile_place_holder){
        
       $upload = move_uploaded_file($this->image_tmp_name, $this->image_destination);
       if($upload){
            $sql = 'INSERT INTO(id, name, idNumber, profileImage, organizationName, password, position, email, status, orgWebsite)
            VALUES(:id, :name, :idNumber, :profileImage, :organizationName, :password, :position, :email, :status, :orgWebsite)';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', NULL);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':idNumber', $this->id_number);
            $stmt->bindParam(':profileImage', $this->image_destination);
            $stmt->bindParam(':organizationName', $this->organization);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':position', $this->position);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':orgWebsite', $this->org_website);
            $result = $stmt->execute();
            if($result == 1){
                return 'success';
            }else{
                return 'failed';
            }
       }else{
           return 'failed';
       }

    }

}

class AccademicSupervisor{
    var $first_name;
    var $last_name;
    var $email;
    var $id_number;
    var $username;
    var $password;

    function __construct($first_name, $last_name, $email, $id_number, $username, $password){
        $this->first_name = $username;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->id_number = $id_number;
        $this->username = $username;
        $this->password = $password;
    }

    function insertAccademicSupervisor($db){
        $sql = 'INSERT INTO accademic_supervisor(id, firstName, lastName, username, idNumber, password, email) 
                                         VALUES(:id, :firstName, :lastName, :username, :idNumber, :password, :email)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', NULL);
        $stmt->bindParam(':firstName', $this->first_name);
        $stmt->bindParam(':lastName', $this->last_name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':idNumber', $this->id_number);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);

        $result = $stmt->execute();
        return $result;
    }
}

class Student{
    var $name;
    var $reg_no;
    var $stud_no;
    var $supervisor_id;

    function __construct($name, $reg_no, $stud_no, $supervisor_id){
        $this->name = $name;
        $this->reg_no = $reg_no;
        $this->stud_no = $stud_no;
        $this->supervisor_id = $supervisor_id;
    }
    			
    public function insertStudent($db){
        $sql = 'INSERT INTO student(id, name, student_number, reg_number, academic_supervisor_id, field_supervisor_id) 
                            VALUES(:id, :name, :student_number, :reg_number, :academic_supervisor_id, :field_supervisor_id)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', NULL);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':student_number', $this->stud_no);
        $stmt->bindParam(':reg_number', $this->reg_no);
        $stmt->bindParam(':academic_supervisor_id', $this->supervisor_id);
        $stmt->bindValue(':field_supervisor_id', NULL);
        $result = $stmt->execute();
        return $result;
    }
}

/**
 * classless functions
 */
function getAccademicSupervisor($db){
    $sql = 'SELECT * FROM accademic_supervisor';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $results;
}

function getStudents($db){
    $sql = 'SELECT * FROM student';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $results;
}

function approve($id, $db){
    $sql = 'UPDATE field_supervisor SET status = :status WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':status', 'approved');
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();
    return $result;
}
function reject($id, $db){
    $sql = 'DELETE FROM field_supervisor WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();
    return $result;
}

function allocateFieldSupervisor($db, $student_number, $supervisor_id){
    $select = 'SELECT field_supervisor_id FROM student WHERE student_number = :student_number';
    $stmt1 = $db->prepare($select);
    $stmt1->bindParam('student_number', $student_number);
    $stmt1->execute();
    $results = $stmt1->fetchColumn();
    if($results == 0){
        $sql = 'UPDATE student SET field_supervisor_id = :field_supervisor_id WHERE student_number = :student_number';
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':field_supervisor_id', $supervisor_id);
        $stmt->bindParam(':student_number', $student_number);
        $result = $stmt->execute();
        return $result;
    }else{
        return 'student already taken';
    }
   
}

function getAdminEmail($db){
    $sql = 'SELECT email FROM admin';
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchColumn();
    return $result;
}