window.laracraft.lock = {

	heartbeat : function($lock){
		$.ajax(window.laracraft.routes.route(window.laracraft.settings.cp_root + '.lock.maintain', {lock:$lock}));
	}
};
