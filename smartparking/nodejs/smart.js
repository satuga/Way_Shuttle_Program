var static = require('node-static');
var http = require('http');
var sockets = [];
var file = new (static.Server)();
var _ = require('underscore')._;
var app = http.createServer(function (req, res) {
                            file.serve(req, res);
							res.writeHead(200, {'Content-Type': 'text/plain'});
	res.end('Smart Parking With Socket connection.');
}).listen(process.env.PORT || 3005);

app.listen(3005, function () {
           console.log('port 3005');
});

var io = require('socket.io').listen(app);
io.sockets.on('connection', function (socket) {
    socket.on('joinGateRoom', function (room_name) {
			console.log("room_name " + room_name);
            socket.join(room_name);
    });
    socket.on('entry_gate', function (room_name) {
		console.log("room_name_entry_gate " + room_name);
        io.sockets.in(room_name).emit('entry_gate_open', 'entry gate opened');
    });
    socket.on('exit_gate', function (room_name) {
		console.log("room_name_exit_gate " + room_name);
        io.sockets.in(room_name).emit('exit_gate_open', 'exit gate opened');
    });
    socket.on('reboot_gate', function (room_name) {
		console.log("room_name_reboot_gate" + room_name);
        io.sockets.in(room_name).emit('reboot_pi_status', 'success');
    });
	socket.on('disconnect', function (room_name) {
        var o = _.findWhere(sockets, {'id': socket.id});
		socket.leave(room_name);
		console.log("room_name_disconnect " + room_name);
        sockets = _.without(sockets, o);
    });
});
