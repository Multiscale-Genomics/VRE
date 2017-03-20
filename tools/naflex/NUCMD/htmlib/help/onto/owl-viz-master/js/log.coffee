 define [], ->
    log = {
    	_log: 	(message, type="log") 	-> 	console[type] message if log.level
    	info :	(message)				->	log._log message
    	warn: 	(message) 				-> 	log._log message, "warn"
    	error: 	(message) 				-> 	log._log message, "error"
    	debug: 	(state) 				->  log.level = state
    }
