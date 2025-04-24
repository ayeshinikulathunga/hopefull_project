<?php
class VisitorInquiry {
    private $db;
    
    public function __construct() {
        $this->db = new Database;
    }
    
    // Add new inquiry
    public function addInquiry($data) {
        // Prepare statement
        $this->db->query('INSERT INTO inquiries (Name, Email, Phone, Subject, Message) VALUES(:name, :email, :phone, :subject, :message)');
        
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':message', $data['message']);
        
        // Execute
        return $this->db->execute();
    }
    
    // Get all inquiries (for admin)
    public function getInquiries() {
        $this->db->query('SELECT * FROM inquiries ORDER BY DateSubmitted DESC');
        
        return $this->db->resultSet();
    }
    
    // Get inquiry by ID (for admin)
    public function getInquiryById($id) {
        $this->db->query('SELECT * FROM inquiries WHERE InquiryID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Update inquiry status (for admin)
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE inquiries SET Status = :status WHERE InquiryID = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }
    
    // Delete inquiry (for admin)
    public function deleteInquiry($id) {
        $this->db->query('DELETE FROM inquiries WHERE InquiryID = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}
