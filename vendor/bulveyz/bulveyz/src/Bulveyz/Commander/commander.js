jQuery(document).ready(function($) {
  var id = 1;
  var token = $('#csrf_token').val();
  $('body').terminal(function(command, term) {
    if (command == 'help') {
      term.echo(
        "make controller      <-- create new controller --> \n" +
        "make model           <-- create new model --> \n" +
        "make -c -m           <-- create new controller and model --> \n" +
        "make auth            <-- create authorization and templates --> \n" +
        "table trash rows     <-- drop all row from table --> \n" +
        "new admin            <-- create new admin account --> \n" +
        "exit                 <-- to leave the branch -->"
      );
    }
    else if (command == 'make controller') {
      term.push(function (command, term) {
            $.post('/bcommander/makecontroller', {command: command, csrf_token: token}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + 'Controller created');
                term.pop();
              }
              else {
                term.error(response);
              }
            });
          },
          {
            prompt: 'Controller name > ',
            name: 'make controller'
          });
    }
    else if (command == 'make model') {
      var history = term.history();
      history.disable();
      term.push(function (command, term) {
            $.post('/bcommander/makemodel', {command: command, csrf_token: token}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + ' model created');
                term.pop();
                history.enable();
              }
              else {
                term.error(response);
              }
            });
          },
          {
            prompt: 'Model name > ',
            name: 'make model'
          });
    }
    else if (command == 'make -c -m') {
      term.push(function (command, term) {
            $.post('/bcommander/makecontrollerandmodel', {command: command, csrf_token: token}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + 'Controller and model created');
                term.pop();
              }
              else {
                term.error(response);
              }
            });
          },
          {
            prompt: 'Controller name > ',
            name: 'make controller and model'
          });
    }
    else if (command == 'make auth') {
      $.get('/bcommander/makeauth', {command: command}).then(function(response) {
        if (response === '')
        {
          term.echo('Auth created');
        }
        else {
          term.error(response);
        }
      });
    }
    else if (command == 'table trash rows') {
      term.push(function (command, term) {
        $.post('/bcommander/trashall', {command: command, csrf_token: token}).then(function(response) {
          if (response === '')
          {
            term.echo('Table rows clear');
            term.pop();
          }
          else {
            term.error(response);
          }
        });
      })
    }
    else {
      term.echo("unknown command " + command);
    }
  }, {
    greetings: "Bulveyz Commander ('help' for all commands)"
  });
});
