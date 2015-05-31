<html>
  <head>
    <title>Index</title>
  </head>
  <body>
    <h3>Index Page</h3>
    <table>
      <?php foreach($users as $user) { ?>
      <tr>
        <td><?php echo $user['userName']; ?></td>
        <td><?php echo $user['dispName']; ?></td>
      </tr>
      <?php } ?>
    </table>
  </body>
</html>