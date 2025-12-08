<?php

class Application
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function create($data)
    {
        try {
            $sql = "INSERT INTO job_applications (
                user_id, company_id, status_id, job_title, job_url, 
                salary, location, job_type, priority, application_date,
                follow_up_date, follow_up_email, interview_date, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['user_id'],
                $data['company_id'],
                $data['status_id'] ?? 1,
                $data['job_title'],
                $data['job_url'] ?? null,
                $data['salary'] ?? null,
                $data['location'] ?? null,
                $data['job_type'] ?? 'WFO',
                $data['priority'] ?? 'Medium',
                $data['application_date'],
                $data['follow_up_date'] ?? null,
                $data['follow_up_email'] ?? null,
                $data['interview_date'] ?? null,
                $data['notes'] ?? null
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Application::create Error: " . $e->getMessage());
            return false;
        }
    }
    public function getByUserId($user_id, $filters = [])
    {
        try {
            $sql = "SELECT 
            ja.*,
            c.name as company_name,
            c.website as company_website,
            ast.status_name
        FROM job_applications ja
        LEFT JOIN companies c ON ja.company_id = c.id
        LEFT JOIN application_statuses ast ON ja.status_id = ast.id
        WHERE ja.user_id = ?";

            $params = [$user_id];

            // Status filter
            if (!empty($filters['status_id'])) {
                $sql .= " AND ja.status_id = ?";
                $params[] = $filters['status_id'];
            }

            // Job type filter
            if (!empty($filters['job_type'])) {
                $sql .= " AND ja.job_type = ?";
                $params[] = $filters['job_type'];
            }

            // Priority filter
            if (!empty($filters['priority'])) {
                $sql .= " AND ja.priority = ?";
                $params[] = $filters['priority'];
            }

            // Search filter
            if (!empty($filters['search'])) {
                $sql .= " AND (c.name LIKE ? OR ja.job_title LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Sorting
            $sortBy = $filters['sort_by'] ?? 'application_date_desc';
            switch ($sortBy) {
                case 'application_date_asc':
                    $sql .= " ORDER BY ja.application_date ASC";
                    break;
                case 'company_asc':
                    $sql .= " ORDER BY c.name ASC";
                    break;
                case 'company_desc':
                    $sql .= " ORDER BY c.name DESC";
                    break;
                case 'priority_desc':
                    $sql .= " ORDER BY FIELD(ja.priority, 'High', 'Medium', 'Low')";
                    break;
                case 'salary_desc':
                    $sql .= " ORDER BY ja.salary IS NULL, ja.salary DESC";
                    break;
                case 'application_date_desc':
                default:
                    $sql .= " ORDER BY ja.application_date DESC";
                    break;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Application::getByUserId Error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id, $user_id)
    {
        try {
            $sql = "SELECT 
                ja.*,
                c.name as company_name,
                c.website as company_website,
                ast.status_name
            FROM job_applications ja
            LEFT JOIN companies c ON ja.company_id = c.id
            LEFT JOIN application_statuses ast ON ja.status_id = ast.id
            WHERE ja.id = ? AND ja.user_id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Application::getById Error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $user_id, $data)
    {
        try {
            $sql = "UPDATE job_applications SET
                company_id = ?,
                status_id = ?,
                job_title = ?,
                job_url = ?,
                salary = ?,
                location = ?,
                job_type = ?,
                priority = ?,
                application_date = ?,
                follow_up_date = ?,
                follow_up_email = ?,
                interview_date = ?,
                notes = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ? AND user_id = ?";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['company_id'],
                $data['status_id'],
                $data['job_title'],
                $data['job_url'] ?? null,
                $data['salary'] ?? null,
                $data['location'] ?? null,
                $data['job_type'],
                $data['priority'],
                $data['application_date'],
                $data['follow_up_date'] ?? null,
                $data['follow_up_email'] ?? null,
                $data['interview_date'] ?? null,
                $data['notes'] ?? null,
                $id,
                $user_id
            ]);
        } catch (PDOException $e) {
            error_log("Application::update Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id, $user_id)
    {
        try {
            $sql = "DELETE FROM job_applications WHERE id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id, $user_id]);
        } catch (PDOException $e) {
            error_log("Application::delete Error: " . $e->getMessage());
            return false;
        }
    }

    public function getStatsByUserId($user_id)
    {
        try {
            $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as applied,
                SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as interview,
                SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) as offer,
                SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) as rejected
            FROM job_applications
            WHERE user_id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Application::getStatsByUserId Error: " . $e->getMessage());
            return [
                'total' => 0,
                'applied' => 0,
                'interview' => 0,
                'offer' => 0,
                'rejected' => 0
            ];
        }
    }

    public function updateStatus($id, $user_id, $status_id)
    {
        try {
            $sql = "UPDATE job_applications 
                    SET status_id = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ? AND user_id = ?";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$status_id, $id, $user_id]);
        } catch (PDOException $e) {
            error_log("Application::updateStatus Error: " . $e->getMessage());
            return false;
        }
    }

    public function getFollowUpsDue($user_id)
    {
        try {
            $sql = "SELECT 
                ja.*,
                c.name as company_name,
                ast.status_name
            FROM job_applications ja
            LEFT JOIN companies c ON ja.company_id = c.id
            LEFT JOIN application_statuses ast ON ja.status_id = ast.id
            WHERE ja.user_id = ? 
            AND ja.follow_up_date IS NOT NULL 
            AND ja.follow_up_date <= CURDATE()
            AND ja.status_id NOT IN (3, 4)
            ORDER BY ja.follow_up_date ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Application::getFollowUpsDue Error: " . $e->getMessage());
            return [];
        }
    }
}