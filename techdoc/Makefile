techdoc.pdf:	techdoc.tex
	rubber -f -d -m xelatex -m index techdoc.tex
	grep 'LaTeX Warning: Reference' techdoc.log

clean:
	rm -f techdoc.aux techdoc.idx techdoc.ilg techdoc.ind techdoc.log techdoc.out techdoc.pdf techdoc.toc


badref:
	grep 'LaTeX Warning: Reference' techdoc.log
