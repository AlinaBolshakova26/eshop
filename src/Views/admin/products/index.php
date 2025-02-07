<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Products</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="container">
    <h1>Product Management</h1>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['price']) ?></td>
                <td>
                    <a href="/admin/products/edit/<?= $product['id'] ?>">Edit</a>
                    <form action="/admin/products/delete/<?= $product['id'] ?>" method="POST">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>