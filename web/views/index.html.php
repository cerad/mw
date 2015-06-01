<html>
  <head>
    <title>Index</title>
  </head>
  <body>
    <h3>Index Page</h3>
    <div id="users" style="display:none">USERS</div>
    <script>
      var app = {};
      
      app.loadUsers = function(success,error) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/users', true);
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
      var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
      };
      app.escape = function(string) { console.log('Esacpe ' + string);
        return String(string).replace(/[&<>"'\/]/g, function (s) {
          return entityMap[s];
        });
      };

      window.onload = function()
      {
        app.loadUsers(app.showUsers);
      };
    </script>
  </body>
</html>