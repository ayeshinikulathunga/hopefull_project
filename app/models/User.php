<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }

    public function register($data) {
        try {
            // Generate UserID (Format: U + 5 random digits)
            $userId = 'U' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            
            // Generate username from email (part before @)
            $username = strstr($data['email'], '@', true);
            
            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Start transaction
            $this->db->query('INSERT INTO users (UserID, Email, Username, PasswordHash, UserType) 
                             VALUES (:userId, :email, :username, :password, "Donor")');
            
            $this->db->bind(':userId', $userId);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':username', $username);
            $this->db->bind(':password', $passwordHash);
            
            if($this->db->execute()) {
                // Generate DonorID (Format: D + 5 random digits)
                $donorId = 'D' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
                
                // Insert donor details
                $this->db->query('INSERT INTO donors (DonorID, UserID, FirstName, LastName, ContactNumber) 
                                 VALUES (:donorId, :userId, :firstName, :lastName, :contactNumber)');
                
                $this->db->bind(':donorId', $donorId);
                $this->db->bind(':userId', $userId);
                $this->db->bind(':firstName', $data['firstName']);
                $this->db->bind(':lastName', $data['lastName']);
                $this->db->bind(':contactNumber', $data['contactNumber']);
                
                return $this->db->execute();
            }
            return false;
            
        } catch(PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            return false;
        }
    }

   

   /* public function login($email, $password) {
        $this->db->query('SELECT users.*, 
                         donors.DonorID, 
                         recipients.RecipientID,
                         recipients.FirstName,
                         recipients.LastName,
                         system_admins.SystemAdminID 
                        auth_moderators.ModeratorID 
                         FROM users 
                         LEFT JOIN donors ON users.UserID = donors.UserID 
                         LEFT JOIN recipients ON users.UserID = recipients.UserID 
                         LEFT JOIN system_admins ON users.UserID = system_admins.UserID 
                         LEFT JOIN auth_moderators ON users.UserID = auth_moderators.UserID
                         WHERE users.Email = :email');
        
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if($row) {
            if(password_verify($password, $row->PasswordHash)) {
                // Update last login time
                $this->db->query('UPDATE users SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userId');
                $this->db->bind(':userId', $row->UserID);
                $this->db->execute();
                
                return $row;
            }
        }
        
        return false;
    }*/

    /*public function login($email, $password) {
        $this->db->query('SELECT users.*, 
                         donors.DonorID, 
                         recipients.RecipientID,
                         recipients.FirstName,
                         recipients.LastName,
                         system_admins.SystemAdminID,
                         auth_moderators.ModeratorID,
                         regional_officers.RegionalOfficerID
                         FROM users 
                         LEFT JOIN donors ON users.UserID = donors.UserID 
                         LEFT JOIN recipients ON users.UserID = recipients.UserID 
                         LEFT JOIN system_admins ON users.UserID = system_admins.UserID 
                         LEFT JOIN auth_moderators ON users.UserID = auth_moderators.UserID
                         LEFT JOIN regional_officers ON users.UserID = regional_officers.UserID
                         WHERE users.Email = :email');
        
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if($row) {
            if(password_verify($password, $row->PasswordHash)) {
                // Update last login time
                $this->db->query('UPDATE users SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userId');
                $this->db->bind(':userId', $row->UserID);
                $this->db->execute();
                
                return $row;
            }
        }
        
        return false;
    }*/

    /*public function login($email, $password) {
        error_log("Starting login process for email: " . $email);
        
        $this->db->query('SELECT users.*, 
                         donors.DonorID, 
                         recipients.RecipientID,
                         recipients.FirstName,
                         recipients.LastName,
                         system_admins.SystemAdminID,
                         auth_moderators.ModeratorID,
                         regional_officers.RegionalOfficerID
                         FROM users 
                         LEFT JOIN donors ON users.UserID = donors.UserID 
                         LEFT JOIN recipients ON users.UserID = recipients.UserID 
                         LEFT JOIN system_admins ON users.UserID = system_admins.UserID 
                         LEFT JOIN auth_moderators ON users.UserID = auth_moderators.UserID
                         LEFT JOIN regional_officers ON users.UserID = regional_officers.UserID
                         WHERE users.Email = :email');
        
        $this->db->bind(':email', $email);
        
        try {
            $row = $this->db->single();
            
            if($row) {
                error_log("User found: " . $row->UserID . ", Email: " . $row->Email);
                error_log("Hash in DB: " . $row->PasswordHash);
                
                // For debugging only - never do this in production
                error_log("Password entered: " . $password);
                
                // Test password verification explicitly
                $verify_result = password_verify($password, $row->PasswordHash);
                error_log("Password verification result: " . ($verify_result ? "TRUE" : "FALSE"));
                
                if($verify_result) {
                    error_log("Password verification successful");
                    
                    // Update last login time
                    $this->db->query('UPDATE users SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userId');
                    $this->db->bind(':userId', $row->UserID);
                    $this->db->execute();
                    
                    error_log("Login successful for user: " . $row->UserID);
                    return $row;
                } else {
                    error_log("Password verification failed for user: " . $row->UserID);
                }
            } else {
                error_log("No user found with email: " . $email);
            }
        } catch (Exception $e) {
            error_log("Exception during login: " . $e->getMessage());
        }
        
        error_log("Login failed for email: " . $email);
        return false;
    }*/

    public function login($email, $password) {
        error_log("Starting login process for email: " . $email);
        
        $this->db->query('SELECT users.*, 
                         donors.DonorID, 
                         recipients.RecipientID,
                         recipients.FirstName,
                         recipients.LastName,
                         system_admins.SystemAdminID,
                         auth_moderators.ModeratorID,
                         regional_officers.RegionalOfficerID,
                         sellers.SellerID
                         FROM users 
                         LEFT JOIN donors ON users.UserID = donors.UserID 
                         LEFT JOIN recipients ON users.UserID = recipients.UserID 
                         LEFT JOIN system_admins ON users.UserID = system_admins.UserID 
                         LEFT JOIN auth_moderators ON users.UserID = auth_moderators.UserID
                         LEFT JOIN regional_officers ON users.UserID = regional_officers.UserID
                         LEFT JOIN sellers ON users.UserID = sellers.UserID
                         WHERE users.Email = :email');
        
        $this->db->bind(':email', $email);
        
        try {
            $row = $this->db->single();
            
            if($row) {
                error_log("User found: " . $row->UserID . ", Email: " . $row->Email);
                error_log("Hash in DB: " . $row->PasswordHash);
                
                // For debugging only - never do this in production
                error_log("Password entered: " . $password);
                
                // Test password verification explicitly
                $verify_result = password_verify($password, $row->PasswordHash);
                error_log("Password verification result: " . ($verify_result ? "TRUE" : "FALSE"));
                
                if($verify_result) {
                    error_log("Password verification successful");
                    
                    // Update last login time
                    $this->db->query('UPDATE users SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userId');
                    $this->db->bind(':userId', $row->UserID);
                    $this->db->execute();
                    
                    error_log("Login successful for user: " . $row->UserID);
                    return $row;
                } else {
                    error_log("Password verification failed for user: " . $row->UserID);
                }
            } else {
                error_log("No user found with email: " . $email);
            }
        } catch (Exception $e) {
            error_log("Exception during login: " . $e->getMessage());
        }
        
        error_log("Login failed for email: " . $email);
        return false;
    }

    // Add findUserByEmail method if not already present
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE Email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        return ($row) ? true : false;
    }

    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE UserID = :id');
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    private function getSellerInfo() {
        $this->db->query('SELECT s.*, u.Username, u.Email, u.RegisteredDate
                         FROM sellers s
                         JOIN users u ON s.UserID = u.UserID
                         WHERE s.SellerID = :sellerId');
        
        $this->db->bind(':sellerId', $_SESSION['seller_id']);
        
        error_log("Attempting to get seller info for SellerID: " . $_SESSION['seller_id']);
        
        $seller = $this->db->single();
        
        if ($seller) {
            error_log("Found seller: " . print_r($seller, true));
        } else {
            error_log("No seller record found for SellerID: " . $_SESSION['seller_id']);
        }
        
        return $seller;
    }



    public function registerRecipient($data) {
    
    try {
        // Generate UserID
        $userId = 'U' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Generate username from email (part before @)
        $username = strstr($data['email'], '@', true);
        
        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $this->db->query('INSERT INTO users (UserID, Email, Username, PasswordHash, UserType) 
                         VALUES (:userId, :email, :username, :password, "Recipient")');
        
        $this->db->bind(':userId', $userId);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':username', $username);
        $this->db->bind(':password', $passwordHash);
        
        if(!$this->db->execute()) {
            throw new Exception('Failed to create user account');
        }

        if($this->db->execute){
        // Generate RecipientID
        $recipientId = 'R' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Insert recipient details with Pending verification status
        $this->db->query('INSERT INTO recipients 
                         (RecipientID, UserID, FirstName, LastName, ContactNumber, 
                          Address, OrganizationType, DocumentationURL, VerificationStatus) 
                         VALUES 
                         (:recipientId, :userId, :firstName, :lastName, :contactNumber, 
                          :address, :organizationType, :documentationURL, "Pending")');
        
        $this->db->bind(':recipientId', $recipientId);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':firstName', $data['firstName']);
        $this->db->bind(':lastName', $data['lastName']);
        $this->db->bind(':contactNumber', $data['contactNumber']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':organizationType', $data['organizationType']);
        $this->db->bind(':documentationURL', $data['documentationURL']);

        return $this->db->execute();
        }
        
        return false;
        
    } catch(PDOException $e) {
        error_log('Recipient registration error: ' . $e->getMessage());
        return false;
    }
}


    // Get donor profile information
    public function getDonorById($donorId) {
        $this->db->query('SELECT d.*, u.Email, u.Username, u.RegisteredDate, u.LastLogin
                        FROM donors d
                        JOIN users u ON d.UserID = u.UserID
                        WHERE d.DonorID = :donorId');
                        
        $this->db->bind(':donorId', $donorId);
        return $this->db->single();
    }
    
    // Update donor profile
    public function updateDonorProfile($data) {
        $this->db->query('UPDATE donors 
                        SET FirstName = :firstName, 
                        LastName = :lastName, 
                        ContactNumber = :contactNumber,
                        AnonymousPreference = :anonymousPreference 
                        WHERE DonorID = :donorId');
                        
        $this->db->bind(':firstName', $data['firstName']);
        $this->db->bind(':lastName', $data['lastName']);
        $this->db->bind(':contactNumber', $data['contactNumber']);
        $this->db->bind(':anonymousPreference', isset($data['anonymousPreference']) ? 1 : 0);
        $this->db->bind(':donorId', $_SESSION['donor_id']);
        
        // Execute first update
        if($this->db->execute()) {
            // Now update email in users table if it was changed
            if(isset($data['email']) && !empty($data['email'])) {
                $this->db->query('UPDATE users 
                                SET Email = :email 
                                WHERE UserID = (SELECT UserID FROM donors WHERE DonorID = :donorId)');
                                
                $this->db->bind(':email', $data['email']);
                $this->db->bind(':donorId', $_SESSION['donor_id']);
                
                return $this->db->execute();
            }
            
            return true;
        }
        
        return false;
    }
    
    // Submit an inquiry
    public function submitInquiry($donorId, $subject, $message) {
        // Generate inquiry ID
        $inquiryId = 'INQ' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Get UserID from donor
        $this->db->query('SELECT UserID FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        $user = $this->db->single();
        
        if($user) {
            $this->db->query('INSERT INTO inquiries (InquiryID, UserID, Subject, Message) 
                            VALUES (:inquiryId, :userId, :subject, :message)');
                            
            $this->db->bind(':inquiryId', $inquiryId);
            $this->db->bind(':userId', $user->UserID);
            $this->db->bind(':subject', $subject);
            $this->db->bind(':message', $message);
            
            return $this->db->execute();
        }
        
        return false;
    }
    
    // Get inquiries by donor
    public function getInquiriesByDonor($donorId) {
        // Get UserID from donor
        $this->db->query('SELECT UserID FROM donors WHERE DonorID = :donorId');
        $this->db->bind(':donorId', $donorId);
        $user = $this->db->single();
        
        if($user) {
            $this->db->query('SELECT * FROM inquiries 
                            WHERE UserID = :userId
                            ORDER BY CreatedDate DESC');
                            
            $this->db->bind(':userId', $user->UserID);
            return $this->db->resultSet();
        }
        
        return [];
    }



/**
 * Verify user password
 * @param string $userId The user ID
 * @param string $password The password to verify
 * @return bool True if password is correct, false otherwise
 */
public function verifyPassword($userId, $password) {
    $this->db->query('SELECT PasswordHash FROM users WHERE UserID = :userId');
    $this->db->bind(':userId', $userId);
    
    $row = $this->db->single();
    
    if (!$row) {
        return false;
    }
    
    $hashedPassword = $row->PasswordHash;
    
    return password_verify($password, $hashedPassword);
}

/**
 * Update user password
 * @param string $userId The user ID
 * @param string $newPassword The new password
 * @return bool True if successful, false otherwise
 */
public function updatePassword($userId, $newPassword) {
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update the password in the database
    $this->db->query('UPDATE users 
                     SET PasswordHash = :password, 
                         LastPasswordChange = CURRENT_TIMESTAMP 
                     WHERE UserID = :userId');
    
    $this->db->bind(':password', $hashedPassword);
    $this->db->bind(':userId', $userId);
    
    return $this->db->execute();
}

public function hasPendingRecipientRequest($userId) {
    $this->db->query('SELECT r.VerificationStatus 
                     FROM recipients r 
                     WHERE r.UserID = :userId');
    $this->db->bind(':userId', $userId);
    $result = $this->db->single();
    
    return ($result && $result->VerificationStatus === 'Pending');
}

public function getRecipientVerificationStatus($userId) {
    $this->db->query('SELECT VerificationStatus FROM recipients WHERE UserID = :userId');
    $this->db->bind(':userId', $userId);
    $result = $this->db->single();
    
    return $result ? $result->VerificationStatus : null;
}

/**
 * Delete user account
 * @param string $userId The user ID
 * @return bool True if successful, false otherwise
 */
public function deleteUser($userId) {
    $this->db->query('DELETE FROM users WHERE UserID = :userId');
    $this->db->bind(':userId', $userId);
    
    return $this->db->execute();
}
    
}