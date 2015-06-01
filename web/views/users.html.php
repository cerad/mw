<html>
  <head>
    <title>Users</title>
  </head>
  <body>
    <h3>Users Page</h3>
    <table>
      <?php foreach($users as $user) { ?>
      <tr>
        <td><?php echo $user['userName']; ?></td>
        <td><?php echo $user['dispName']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="users">USERS</div>
    <script>
      var users = JSON.parse('<?php echo $usersx; ?>');
      
      var html = '<span>' + 'Spanx' + '</span>';
      var rows = '';
      users.forEach(function(user) {
        rows +=
        '<tr>' +
          '<td>' + user.userName + '</td>' + 
          '<td>' + user.dispName + '</td>' +
        '</tr>'; 
      });
      //var table = '<table>' + rows + '</table>';
      var table = document.createElement('table'); //.innerHTML = rows;
      table.innerHTML = rows;
      
      var usersElement = document.getElementById('users');
      
    //usersElement.innerHTML = table;
      usersElement.parentNode.replaceChild(table,usersElement);
    </script>
  </body>
</html>