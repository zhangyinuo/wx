var fs = require('fs'),
	system = require('system');

if (system.args.length < 2) {
	console.log("Usage: readFile.js FILE");
	phantom.exit(1);
}

var content = '',
	f = null,
	lines = null,
	eol = system.os.name == 'windows' ? "\r\n" : "\n";

try {
	f = fs.open(system.args[1], "r");
	content = f.read();
} catch (e) {
	console.log(e);
}

if (f) {
	f.close();
}

if (content) {
	lines = content.split(eol);
	for (var i = 0, len = lines.length; i < len; i++) {
		console.log(lines[i]);
	}
}

phantom.exit();
