<html>
  <head>
    <title>Index</title>
  </head>
  <body>
    <h3>Index Page</h3>
    <table>
      <?php foreach($users as $user) { ?>
      <tr>
        <td><?php echo $user['user_name']; ?></td>
        <td><?php echo $user['disp_name']; ?></td>
      </tr>
      <?php } ?>
    </table>
  </body>
</html>