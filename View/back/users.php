<?php
require_once "../../Model/User.php";
$users = User::getAllUsers();
?>

<table border="1">
<tr>
<th>ID</th>
<th>Nom</th>
<th>Email</th>
</tr>

<?php foreach($users as $user): ?>
<tr>
<td><?= $user['id'] ?></td>
<td><?= $user['nom'] ?></td>
<td><?= $user['prenom'] ?></td>
<td><?= $user['email'] ?></td>
<td><?= $user['telephone'] ?></td>
</tr>
<?php endforeach; ?>
</table>