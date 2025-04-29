<?php
// Connect to the database (replace with your actual connection details)
include "connection.php" ;

// Initialize variables for filtering and searching
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';
$priority = isset($_GET['priority']) ? mysqli_real_escape_string($con, $_GET['priority']) : '';
$sort_by = isset($_GET['sort_by']) ? mysqli_real_escape_string($con, $_GET['sort_by']) : 'project_name';
$sort_order = isset($_GET['sort_order']) ? mysqli_real_escape_string($con, $_GET['sort_order']) : 'ASC';

// Determine current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5; // Number of records per page
$start_from = ($page - 1) * $per_page;

// Build the base query
$query = "SELECT * FROM project WHERE 1=1";

// Add search condition if provided
if (!empty($search)) {
    $query .= " AND (project_name LIKE '%$search%' OR project_descp LIKE '%$search%')";
}

// Add status filter if provided
if (!empty($status)) {
    $query .= " AND project_status = '$status'";
}

// Add priority filter if provided
if (!empty($priority)) {
    $query .= " AND project_priority = '$priority'";
}

// Add sorting
$query .= " ORDER BY $sort_by $sort_order";

// Add pagination
$query_for_count = $query;
$query .= " LIMIT $start_from, $per_page";

// Execute query
$result = mysqli_query($con, $query);

// Count total records for pagination
$count_result = mysqli_query($con, $query_for_count);
$total_records = mysqli_num_rows($count_result);
$total_pages = ceil($total_records / $per_page);

// Get unique status values for dropdown
$status_query = "SELECT DISTINCT project_status FROM project WHERE project_status IS NOT NULL";
$status_result = mysqli_query($con, $status_query);

// Get unique priority values for dropdown
$priority_query = "SELECT DISTINCT project_priority FROM project WHERE project_priority IS NOT NULL";
$priority_result = mysqli_query($con, $priority_query);

// Function to generate sort links
function getSortLink($field, $current_sort, $current_order) {
    $new_order = ($current_sort == $field && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    
    if ($current_sort == $field) {
        $icon = ($current_order == 'ASC') ? '<i class="fas fa-sort-up ml-1"></i>' : '<i class="fas fa-sort-down ml-1"></i>';
    } else {
        $icon = '<i class="fas fa-sort ml-1 text-gray-400"></i>';
    }
    
    $params = $_GET;
    $params['sort_by'] = $field;
    $params['sort_order'] = $new_order;
    $query_string = http_build_query($params);
    
    return '<a href="?' . $query_string . '" class="text-white hover:text-green-400">' . ucfirst(str_replace('project_', '', $field)) . $icon . '</a>';
}

// Function to generate status badge
function getStatusBadge($status) {
    $color = 'gray';
    
    switch(strtolower($status)) {
        case 'active':
            $color = 'green';
            break;
        case 'completed':
            $color = 'blue';
            break;
        case 'in review':
            $color = 'yellow';
            break;
        case 'on hold':
            $color = 'orange';
            break;
        case 'cancelled':
            $color = 'red';
            break;
    }
    
    return '<span class="bg-' . $color . '-500 text-xs px-2 py-1 rounded-full">' . $status . '</span>';
}

// Function to generate priority indicator
function getPriorityIndicator($priority) {
    $color = 'gray';
    
    switch(strtolower($priority)) {
        case 'high':
            $color = 'red';
            break;
        case 'medium':
            $color = 'yellow';
            break;
        case 'low':
            $color = 'green';
            break;
    }
    
    return '<div class="flex items-center">
                <span class="inline-block w-2 h-2 bg-' . $color . '-500 rounded-full mr-1"></span>
                <span>' . $priority . '</span>
            </div>';
}
?>

<!-- Project Search and Filter -->
<div class="bg-gray-800 p-4 rounded-xl mb-6 card-hover transition-all duration-300">
    <form method="GET" action="" id="filterForm">
        <input type="hidden" name="sort_by" value="<?php echo htmlspecialchars($sort_by); ?>">
        <input type="hidden" name="sort_order" value="<?php echo htmlspecialchars($sort_order); ?>">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="mb-4 md:mb-0 relative">
                <input type="text" name="search" placeholder="Search projects..." class="bg-gray-900 border border-gray-700 rounded-lg p-2 pl-10 w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" value="<?php echo htmlspecialchars($search); ?>">
                <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                <?php if (!empty($search)): ?>
                    <button type="button" onclick="document.querySelector('input[name=search]').value=''; document.getElementById('filterForm').submit();" class="absolute right-3 top-3 text-gray-500 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
                <select name="status" class="bg-gray-900 border border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Status: All</option>
                    <?php while ($status_row = mysqli_fetch_assoc($status_result)): ?>
                        <option value="<?php echo htmlspecialchars($status_row['project_status']); ?>" <?php echo ($status == $status_row['project_status']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($status_row['project_status']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <select name="priority" class="bg-gray-900 border border-gray-700 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Priority: All</option>
                    <?php while ($priority_row = mysqli_fetch_assoc($priority_result)): ?>
                        <option value="<?php echo htmlspecialchars($priority_row['project_priority']); ?>" <?php echo ($priority == $priority_row['project_priority']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($priority_row['project_priority']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <?php if (!empty($search) || !empty($status) || !empty($priority)): ?>
                    <button type="button" onclick="window.location='project.php'" class="bg-gray-700 hover:bg-gray-600 text-white rounded-lg p-2 focus:outline-none">
                        <i class="fas fa-undo mr-1"></i> Reset Filters
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<!-- Assigned Projects Section with Table -->
<div class="bg-gray-800 rounded-xl p-6 mb-8 card-hover transition-all duration-300">
    <?php if ($total_records > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_name', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_descp', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_status', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_priority', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_progress', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            <?php echo getSortLink('project_end_date', $sort_by, $sort_order); ?>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Team</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium"><?php echo htmlspecialchars($row['project_name']); ?></div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-gray-400"><?php echo htmlspecialchars($row['project_descp']); ?></div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <?php echo getStatusBadge($row['project_status']); ?>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <?php echo getPriorityIndicator($row['project_priority']); ?>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-900 rounded-full overflow-hidden mr-2">
                                    <div class="bg-green-500 h-2" style="width: <?php echo (int)$row['project_progress']; ?>%"></div>
                                </div>
                                <span class="text-xs"><?php echo (int)$row['project_progress']; ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <?php 
                                $date = new DateTime($row['project_end_date']);
                                echo $date->format('M d, Y'); 
                                ?>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <?php
                            // Here you would typically join with a team members table
                            // For now we'll just display a placeholder
                            ?>
                            <div class="flex -space-x-2">
                                <img src="../images/Profile/guest.png" alt="Team Member" class="w-6 h-6 rounded-full border-2 border-gray-800">
                                <img src="../images/Profile/guest.png" alt="Team Member" class="w-6 h-6 rounded-full border-2 border-gray-800">
                                <div class="w-6 h-6 rounded-full bg-gray-700 border-2 border-gray-800 flex items-center justify-center text-xs">+2</div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <a href="view_project.php?id=<?php echo $row['project_id']; ?>" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-xs font-medium">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-400">
                Showing <span class="font-medium"><?php echo $start_from + 1; ?>-<?php echo min($start_from + $per_page, $total_records); ?></span> of <span class="font-medium"><?php echo $total_records; ?></span> projects
            </div>
            <?php if ($total_pages > 1): ?>
                <div class="flex space-x-1">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-white">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <button disabled class="px-3 py-1 rounded bg-gray-900 text-gray-500 cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php
                    // Display page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>" class="px-3 py-1 rounded <?php echo $i == $page ? 'bg-green-600 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&priority=<?php echo urlencode($priority); ?>&sort_by=<?php echo urlencode($sort_by); ?>&sort_order=<?php echo urlencode($sort_order); ?>" class="px-3 py-1 rounded bg-gray-700 hover:bg-gray-600 text-white">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <button disabled class="px-3 py-1 rounded bg-gray-900 text-gray-500 cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-12">
            <i class="fas fa-search text-gray-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-medium text-gray-400 mb-2">No projects found</h3>
            <p class="text-gray-500">Try adjusting your search or filter criteria</p>
            <?php if (!empty($search) || !empty($status) || !empty($priority)): ?>
                <button onclick="window.location='project.php'" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-undo mr-2"></i>Reset Filters
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Add this JavaScript to make the search execute on Enter key press
document.querySelector('input[name="search"]').addEventListener('keyup', function(event) {
    if (event.key === 'Enter') {
        document.getElementById('filterForm').submit();
    }
});

// Add animation for table rows
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});
</script>