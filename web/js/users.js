(function(React,Cerad) {
  'use strict';
  // Used for testing
  var users = [
    {"id":1,"userName":"ahundiak","dispName":"Art Hundiak",      "email":"ahundiak@example.com","roles":null},
    {"id":2,"userName":"bclinton","dispName":"Bill Clinton",     "email":"bclinton@example.com","roles":null},
    {"id":3,"userName":"hclinton","dispName":"Hillary Clinton",  "email":"hclinton@example.com","roles":null},
    {"id":4,"userName":"cclinton","dispName":"Chelse Clinton",   "email":"cclinton@example.com","roles":null},
    {"id":5,"userName":"gomally", "dispName":"George O'Ma<br>ly","email":"gomally@example.com", "roles":null}
  ];
  var UserRow = React.createClass({
    render: function() {
      var user = this.props.user;
      var rowIndex = this.props.rowIndex;
      
      var trProps = { className: 'odd' };
      
      if ((rowIndex % 2) === 0) {
        trProps.className = 'even';
      }
      var href = app.getApiLinkHref(user.links,'self');
      var link = React.createElement('a',{href: href},user.id);
      
      var row = React.createElement('tr',trProps,[
        React.createElement('td',null,link),
        React.createElement('td',null,user.userName),
        React.createElement('td',null,user.dispName),
        React.createElement('td',null,user.email)
      ]);
      return row;
    }
  });
  var UserTable = React.createClass({
    render: function() {
      var rows = [];
      var rowIndex = 0;
      var rowFactory = React.createFactory(UserRow);
      this.props.users.forEach(function(user) {
        rows.push(rowFactory({ 
          user: user, 
          key:  user.id, 
          rowIndex: ++rowIndex
        }));
      }.bind(this));
      return React.createElement('table',null,[
        React.createElement('thead',null,[
          React.createElement('tr',null,[
            React.createElement('th',null,'Row'),
            React.createElement('th',null,'User Name'),
            React.createElement('th',{ className: 'ok'},'Display Name'),
            React.createElement('th',null,'Email')       
          ])          
        ]),
        React.createElement('tbody',null,rows)
      ]);
    }
  });
  var QueryUserTable = React.createClass({
    getInitialState: function() {
      return { users: [] };
    },
    componentDidMount: function() {
      app.getJson(this.props.apiHrefUsers,function(data) {
        this.setState({ users: data});
      }.bind(this));
      return;      
    },
    render: function() {
      var tableFactory = React.createFactory(UserTable);
      return tableFactory({
        users: this.state.users
      });
    }
  });
  app.usersTableFactory = React.createFactory(QueryUserTable);
})(React,app);
