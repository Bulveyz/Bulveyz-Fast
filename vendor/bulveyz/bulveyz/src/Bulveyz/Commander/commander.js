jQuery(document).ready(function($) {
  var id = 1;
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
            $.post('http://bloge.test/bcommander/makecontroller', {command: command}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + 'Controller created');
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
      term.push(function (command, term) {
            $.post('http://bloge.test/bcommander/makemodel', {command: command}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + ' model created');
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
            $.post('http://bloge.test/bcommander/makecontrollerandmodel', {command: command}).then(function(response) {
              if (response === '')
              {
                term.echo(command.charAt(0).toUpperCase() + command.slice(1) + 'Controller and model created');
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
      $.get('http://bloge.test/bcommander/makeauth', {command: command}).then(function(response) {
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
        $.post('http://bloge.test/bcommander/trashall', {command: command}).then(function(response) {
          if (response === '')
          {
            term.echo('Table rows clear');
          }
          else {
            term.error(response);
          }
        });
      })
    }
    else if (command == 'new admin') {
      term.push(function (command, term) {
            $.post('http://bloge.test/bcommander/newadmin', {command: command}).then(function(response) {
              if (response === '')
              {
                term.echo('admin added');
              }
              else {
                term.error(response);
              }
            });
          },
          {
            prompt: 'Name | Password | Project Key > '
          });
    }
    else {
      term.echo("unknown command " + command);
    }
  }, {
    greetings: "Bulveyz Commander ('help' for all commands)"
  });
});

console.log('asdasd'.charAt(0).toUpperCase() + 'asdasd'.slice(1))