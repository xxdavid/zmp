all:
	php lib/texy2latex.php
	cd latex; latexmk zmp.tex -pdf
	mv latex/zmp.pdf .
	./openPreview.sh
