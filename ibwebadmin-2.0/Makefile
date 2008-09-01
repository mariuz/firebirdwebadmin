tags:
	find 	. -name '*.php' -print | \
	etags 	--language=none \
		--regex='/[ \t]*\(function\|class\)[ \t]+[^ \t(]+/' \
	 	--regex='/[ \t]*define[ \t]*("[a-zA-Z0-9_]+"/' -

clean:
	find	. -name '*~' -print | xargs rm -f

