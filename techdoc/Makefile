techdoc.pdf:	techdoc.tex
	rubber -f -d -m xelatex -m index techdoc.tex
	if grep 'LaTeX Warning: Reference' techdoc.log; then exit 1; else exit 0; fi

clean:
	rm -f techdoc.aux techdoc.idx techdoc.ilg techdoc.ind techdoc.log techdoc.out techdoc.pdf techdoc.toc


badref:
	grep 'LaTeX Warning: Reference' techdoc.log
