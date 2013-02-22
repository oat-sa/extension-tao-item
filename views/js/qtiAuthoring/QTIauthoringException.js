function QTIauthoringException(name, message){
	
	try{
		throw new Error('');//artificially throw an error to get the call stack
	}catch(e){
		e.stack = e.stack.split("@"+e.fileName+":").join(":");
		var fullStack = e.stack.split("\\n");
		var stack = [];
		var lineNumber = 0;
		stack[0] = "Exception: "+name+"(\""+message+"\")"
		for (var i=2;i<fullStack.length-3;i++) {
			var entry = fullStack[i];
			var entry_detailed = entry.split(":");
			entry_detailed[1] = entry_detailed[1] - 4; // THIS is to
			// mark, that we'll "move" the source 4 lines higher,
			// ... because it's eval code executed. Remove that for
			// clear values.
			if (i==2) lineNumber = entry_detailed[1];
			stack[i] = entry_detailed.join(":");
		}
		
		var returnValue = {
			name:name,
			message:message,
			stack:stack.join("\\n"),
			lineNumber:lineNumber
		};
		return returnValue;
	}
	
}
