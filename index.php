<?php
require 'db.php';

// Handle Create Operation
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        header("Location: index.php?message=Record created successfully");
        exit();
    } catch (PDOException $e) {
        die("Error creating record: " . $e->getMessage());
    }
}

// Handle Update Operation (Form Submission)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        header("Location: index.php?message=Record updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Error updating record: " . $e->getMessage());
    }
}

// Handle Delete Operation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: index.php?message=Record deleted successfully");
        exit();
    } catch (PDOException $e) {
        die("Error deleting record: " . $e->getMessage());
    }
}

// Fetch All Users (Read Operation)
try {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Fetch User for Edit Form
$userToEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $userToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching user for edit: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple CRUD</title>
    <style>
        body { font-family: sans-serif; }
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h2, h3 { text-align: center; }
        form { width: 50%; margin: 20px auto; padding: 15px; border: 1px solid #ddd; }
        input[type=text], input[type=email], button { width: calc(100% - 12px); padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; box-sizing: border-box; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .edit, .delete { text-decoration: none; padding: 5px 10px; margin-right: 5px; border-radius: 5px; }
        .edit { background-color: #007bff; color: white; }
        .delete { background-color: #dc3545; color: white; }
        .message { text-align: center; color: green; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Simple CRUD Operations</h2>

    <?php if (isset($_GET['message'])): ?>
        <p class="message"><?php echo $_GET['message']; ?></p>
    <?php endif; ?>

    <h3>Add New User</h3>
    <form method="post">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <button type="submit" name="create">Add User</button>
    </form>

    <?php if ($userToEdit): ?>
        <h3>Edit User</h3>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $userToEdit['id']; ?>">
            <input type="text" name="name" value="<?php echo $userToEdit['name']; ?>" required><br>
            <input type="email" name="email" value="<?php echo $userToEdit['email']; ?>" required><br>
            <button type="submit" name="update">Update User</button>
        </form>
    <?php endif; ?>

    <h3>User List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <a href="index.php?edit=<?php echo $user['id']; ?>" class="edit">Edit</a>
                            <a href="index.php?delete=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
