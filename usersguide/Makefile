usersguide.pdf:	usersguide.tex
	rubber -f -d -m xelatex -m index usersguide.tex
	if grep 'LaTeX Warning: Reference' usersguide.log; then exit 1; else exit 0; fi

clean:
	rm -f usersguide.aux usersguide.idx usersguide.ilg usersguide.ind usersguide.log usersguide.out usersguide.pdf usersguide.toc

badref:
	grep 'LaTeX Warning: Reference' usersguide.log

