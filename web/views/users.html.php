<!DOCTYPE html>
<html>
  <head>
    <title>React</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/JSXTransformer.js"></script>
    <style>
      .ok { color: green }
      tr.odd  { background-color: lightgreen; }
      tr.even { background-color: lightgray; }
    </style>
  </head>
  <body>
    <h3>React Testing</h3>
    <a href="<?php echo $urlGenerator('app_index'); ?>">Home Page</a>
    <div id="static-table"><span>Static User Table</span>
      <?php echo $userTable->renderToString() . "\n"; ?>
    </div>
    <div id="users">Users Table</div>
    <div id="products">Product Table</div>
    
    <script src="js/cerad.js"></script>
    <script>
      var app = new CeradApp();
    </script>
    <script src="js/users.js"    type="text/javascript"></script>
    <!-- <script src="js/products.js" type="text/jsx"></script> -->
    <script>
      (function(React,app) {
        app.getJson('/api',function(links) {
          app.apiLinks = links;
          app.apiHrefUsers = app.getApiLinkHref(links,'users');
          React.render(app.usersTableFactory({ apiHrefUsers: app.apiHrefUsers}), document.getElementById('users'));
        });
      })(React,app);
    </script>
  </body>
</html>
