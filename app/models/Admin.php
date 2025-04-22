<?php
class Admin {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    // Get total users count by type
    public function getUserCounts() {
        $this->db->query('SELECT 
            UserType, 
            COUNT(*) as count 
            FROM users 
            GROUP BY UserType');
        
        return $this->db->resultSet();
    }

    // Get recent user registrations
    public function getRecentUsers($limit = 10) {
        $this->db->query('SELECT 
            UserID, 
            Email, 
            Username, 
            UserType,
            UserStatus, 
            RegisteredDate 
            FROM users 
            ORDER BY RegisteredDate DESC 
            LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Get all users with optional filtering
    public function getAllUsers($userType = null, $userStatus = null) {
        $sql = 'SELECT 
            UserID, 
            Email, 
            Username, 
            UserType,
            UserStatus, 
            RegisteredDate 
            FROM users WHERE 1=1';
            
        if($userType) {
            $sql .= ' AND UserType = :userType';
        }
        
        if($userStatus) {
            $sql .= ' AND UserStatus = :userStatus';
        }
        
        $sql .= ' ORDER BY RegisteredDate DESC';
        
        $this->db->query($sql);
        
        if($userType) {
            $this->db->bind(':userType', $userType);
        }
        
        if($userStatus) {
            $this->db->bind(':userStatus', $userStatus);
        }
        
        return $this->db->resultSet();
    }

    // Get pending verification requests
    public function getPendingVerifications() {
        $this->db->query('SELECT 
            r.RecipientID, 
            u.Email, 
            r.FirstName, 
            r.LastName, 
            r.OrganizationType, 
            r.DocumentationURL
            FROM recipients r
            JOIN users u ON r.UserID = u.UserID
            WHERE r.VerificationStatus = "Pending"');
        
        return $this->db->resultSet();
    }

    // Update user status
    public function updateUserStatus($userId, $status) {
        $this->db->query('UPDATE users SET UserStatus = :status WHERE UserID = :userId');
        $this->db->bind(':status', $status);
        $this->db->bind(':userId', $userId);
        
        return $this->db->execute();
    }

    // Add new user
    // Add new user
public function addUser ($data) {
    // Generate a UserID that starts with 'U' and is followed by a unique number
    do {
        $userID = 'U' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $this->db->query("SELECT * FROM users WHERE UserID = :userid");
        $this->db->bind(':userid', $userID);
        $this->db->execute();
        $existing = $this->db->rowCount();
    } while ($existing > 0);
    
    // Construct the SQL query with the values directly
    $query = "INSERT INTO users (UserID, Email, Username, PasswordHash, UserType, UserStatus) 
              VALUES ('$userID', :email, :username, :password_hash, :user_type, :user_status)";

    // Execute the query
    $this->db->query($query); // Use query to execute the SQL statement

    // Bind the parameters
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':password_hash', $data['password']); // Store the hashed password
    $this->db->bind(':user_type', $data['user_type']);
    $this->db->bind(':user_status', $data['user_status']);

    // Execute the query and return the result
    return $this->db->execute(); // Returns true on success
}

public function getUserById($id) {
    $this->db->query("SELECT * FROM users WHERE UserID = :id");
    $this->db->bind(':id', $id);
    return $this->db->single();
}

public function editUser($data) {
    $this->db->query("UPDATE users SET Email = :email, Username = :username, UserType = :user_type, UserStatus = :user_status WHERE UserID = :userID");
    
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':user_type', $data['user_type']);
    $this->db->bind(':user_status', $data['user_status']);
    $this->db->bind(':userID', $data['userID']);

    return $this->db->execute();
}



    // Delete user
    public function deleteUser($userId) {
        $this->db->query('DELETE FROM users WHERE UserID = :userId');
        $this->db->bind(':userId', $userId);
        
        return $this->db->execute();
    }

    // Verify recipient
    public function verifyRecipient($recipientId, $status) {
        $this->db->query('UPDATE recipients 
                          SET VerificationStatus = :status 
                          WHERE RecipientID = :recipientId');
        $this->db->bind(':status', $status);
        $this->db->bind(':recipientId', $recipientId);
        
        return $this->db->execute();
    }

    public function approve($recipientID) {
        $this->db->query('UPDATE recipients SET VerificationStatus = :status WHERE RecipientID = :recipientID');
        $this->db->bind(':status', 'Approved'); // Use a string for the status
        $this->db->bind(':recipientID', $recipientID);
        
        return $this->db->execute(); // Execute the query
    }
    
    public function reject($recipientID) {
        $this->db->query('UPDATE recipients SET VerificationStatus = :status WHERE RecipientID = :recipientID');
        $this->db->bind(':status', 'Rejected'); // Use a string for the status
        $this->db->bind(':recipientID', $recipientID);
        
        return $this->db->execute(); // Execute the query
    }

    public function generateUserId() {
        do {
            // Generate a random 5-digit number
            $randomNumber = rand(10000, 99999);
            $userId = 'U' . $randomNumber;
    
            // Check if the user ID already exists in the database
            $query = "SELECT COUNT(*) FROM users WHERE UserID = :userId";
            $this->db->query($query);
            $this->db->bind(':userId', $userId);
            $count = $this->db->single(); // Get the count of existing user IDs
    
        } while ($count > 0); // Repeat until a unique ID is found
    
        return $userId; // Return the unique user ID
    }

}
?>



