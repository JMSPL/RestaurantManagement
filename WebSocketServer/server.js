
var app = require('http').createServer();
var io = require('socket.io')(app);

var LoggedUsers = require('./loggedusers.js');
var loggedUsers = new LoggedUsers();

app.listen(8080, function(){
    console.log('listening on *:8080');
});


io.on('connection', function (socket) {
    console.log('client has connected (socket ID = '+socket.id+')' );

    socket.on('user_enter', function (user) {
        if (user !== undefined && user !== null){
            socket.join(user.type);
            loggedUsers.addUserInfo(user, socket.id);
        }
    });

    socket.on('meal_paid', function(meal){
        io.to('manager').emit('meal_paid', meal); //ENVIA MENSAEGM PARA OS MANAGERS se quiseres mandar por exemplo para waoters metes aqui
    });

    socket.on('meal_terminated', function(meal){
        io.to('manager').emit('meal_terminated', meal);
        io.sockets.to('invoiceListener', 'pendingInvoice');
    });

    socket.on('invoicePendingChanged', function(message){
    	if(message == 'register'){
    		socket.join('invoiceListener');
    	}
    	else if(message == 'unregister'){
    		socket.leave('invoiceListener');
    	}
    	else{
    		io.sockets.to('invoiceListener', message);
    	}
    });

    socket.on('problem', function(problem){
        socket.broadcast.to('manager_shift_active').emit('notify_problem', problem);
    });

    socket.on('shift_start', function(user){
        socket.join('shift_active');
        if(user.type === 'manager'){
            socket.join('manager_shift_active');
        }
        console.log(user.intern);
        if(user.intern == 1){
            io.sockets.to('manager_shift_active').emit('internShiftStart', user.name);
        }
    });

    socket.on('shift_end', function(user){
        socket.leave('shift_active');
        if(user.type === 'manager'){
            socket.leave('manager_shift_active');
        }
    });

    socket.on('moreThan5Customers', function(message){
        io.sockets.to('manager_shift_active').emit('moreThan5Invoice', 'whatever');
    });
});

