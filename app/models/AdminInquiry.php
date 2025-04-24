<?php
class AdminInquiry {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all inquiries with optional pagination
    public function getInquiries($limit = null, $offset = 0) {
        $sql = "SELECT * FROM inquiries ORDER BY DateSubmitted DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $this->db->query($sql);
            $this->db->bind(':limit', $limit);
            $this->db->bind(':offset', $offset);
        } else {
            $this->db->query($sql);
        }

        return $this->db->resultSet();
    }

    // Get inquiry by ID
    public function getInquiryById($id) {
        $this->db->query("SELECT * FROM inquiries WHERE InquiryID = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update inquiry status
    public function updateStatus($id, $status) {
        $this->db->query("UPDATE inquiries SET Status = :status WHERE InquiryID = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    // Delete inquiry
    public function deleteInquiry($id) {
        $this->db->query("DELETE FROM inquiries WHERE InquiryID = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Count inquiries by status
    public function countInquiriesByStatus($status = null) {
        if ($status) {
            $this->db->query("SELECT COUNT(*) as count FROM inquiries WHERE Status = :status");
            $this->db->bind(':status', $status);
        } else {
            $this->db->query("SELECT COUNT(*) as count FROM inquiries");
        }
        $result = $this->db->single();
        return $result->count;
    }

    // Get recent inquiries (for dashboard)
    public function getRecentInquiries($limit = 5) {
        $this->db->query("SELECT * FROM inquiries WHERE Status = 'New' ORDER BY DateSubmitted DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Search inquiries
    public function searchInquiries($keyword) {
        $this->db->query("SELECT * FROM inquiries WHERE 
                         Name LIKE :keyword OR 
                         Email LIKE :keyword OR 
                         Subject LIKE :keyword OR 
                         Message LIKE :keyword 
                         ORDER BY DateSubmitted DESC");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultSet();
    }
    public function getInquiriesByStatus($status) {
        $this->db->query("SELECT * FROM inquiries WHERE Status = :status ORDER BY DateSubmitted DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }
}