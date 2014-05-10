var page = require('webpage').create(),
    system = require('system'),
	token= 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=TOKEN';

	phantom.cookiesEnabled = true;

page.onConsoleMessage = function(msg) { 
	console.log(msg);
};

token = token.replace(/TOKEN/g, system.args[1]);
data = system.args[2];
page.open(token, 'post', data, function(r1) {
	if(r1 !== 'success') {
		console.log('Unable to open host');
		phantom.exit();
	} else {
		console.log(page.content);
		phantom.exit();
	}
});

