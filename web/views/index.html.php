<html>
  <head>
    <title>Index</title>
  </head>
  <body>
    <h3>Index Page Route</h3>
    <a href="<?php echo $urlGenerator('app_users'); ?>">Users Page</a>
    <div id="users" style="display:none">USERS</div>
    <br/>
    <div id="games" style="display:none">GAME</div>
    <script>
      var app = {};
      
      app.loadUsers = function(success,error) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/users', true);
        xhr.responseType = 'json';
        xhr.onload = function() {
          var status = xhr.status;
          if (status === 200) {
            success(xhr.response);
          } else {
            alert('Error ' + status);
          }
        };
        xhr.send();
      };
      app.loadGame = function(success,error) {
        var xhr = new XMLHttpRequest();
      //xhr.open('GET', 'http://sportacus.zayso.org/api/projects/19/games/7651?pin=9345', true);
        xhr.open('GET', 'http://local.sportacus.zayso.org/api/projects/19/games/7651?pin=9345', true);
        xhr.responseType = 'json';
        xhr.onload = function() {
          var status = xhr.status;
          if (status === 200) {
            success(xhr.response);
          } else {
            alert('Error ' + status);
          }
        };
        xhr.send();
      };

      app.showUsers = function(users) {
        var rows = '';
        users.forEach(function(user) { rows +=
          '<tr>' +
            '<td>' + app.escape(user.userName) + '</td>' + 
            '<td>' + app.escape(user.dispName) + '</td>' +
          '</tr>'; 
        });
        var table = document.createElement('table');
        table.innerHTML = rows;
      
        var usersElement = document.getElementById('users');
      
        usersElement.parentNode.replaceChild(table,usersElement);  
      };
     app.showGames = function(info) {
        var games  = info.games;
        var fields = info.fields;
        var rows  = '<tr><th>ID</th><th>NUM</th><th>DATE</th><th>TIME</th></tr>';
        games.forEach(function(game) { rows +=
          '<tr>' +
            '<td>' + app.escape(game.id)   + '</td>' + 
            '<td>' + app.escape(game.num)  + '</td>' +
            '<td>' + app.escape(game.date) + '</td>' +
            '<td>' + app.escape(game.time) + '</td>' +
            '<td>' + app.escape(fields[game.fieldId].name) + '</td>' +
          '</tr>'; 
        });
        var table = document.createElement('table');
        table.innerHTML = rows;
      
        var gamesElement = document.getElementById('games');
      
        gamesElement.parentNode.replaceChild(table,gamesElement);  
      };
      var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
      };
      app.escape = function(string) {
        return String(string).replace(/[&<>"'\/]/g, function (s) {
          return entityMap[s];
        });
      };

      window.onload = function()
      {
        app.loadUsers(app.showUsers);
        app.loadGame (app.showGames);
      };
    </script>
  </body>
</html>