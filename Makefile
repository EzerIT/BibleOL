ALL_TARGETS = js/ol.js js/fontselector.js js/editquiz.js js/handle_legend.js styles/ol.css styles/ol_zh.css

all:	$(ALL_TARGETS) allugly

js/ol.js:	ts/ol.ts ts/answer.ts ts/charset.ts ts/componentwithyesno.ts ts/configuration.ts ts/dictionary.ts \
	ts/sentencegrammar.ts ts/grammarselectionbox.ts ts/displaymonadobject.ts \
	ts/localization.ts ts/monadobject.ts ts/panelquestion.ts \
	ts/statistics.ts ts/stringwithsort.ts ts/util.ts ts/quiz.ts ts/quizdata.ts ts/resizer.ts \
	ts/localization_general.ts
	tsc --removeComments --noImplicitAny --out $@ $<

js/editquiz.js:	ts/editquiz.ts ts/configuration.ts ts/localization.ts \
	ts/paneltemplmql.ts ts/paneltemplsentenceselector.ts ts/paneltemplquizobjectselector.ts \
	ts/paneltemplquizfeatures.ts ts/sentencegrammar.ts \
	ts/stringwithsort.ts ts/sortingcheckbox.ts ts/util.ts ts/verbclasspanel.ts ts/resizer.ts \
	ts/charset.ts ts/localization_general.ts
	tsc --removeComments --noImplicitAny  --out $@ $<

js/fontselector.js:	ts/fontselector.ts ts/fontdetect.d.ts ts/util.ts ts/localization_general.ts
	tsc --removeComments --noImplicitAny --out $@ $<

js/handle_legend.js: ts/handle_legend.ts ts/util.ts ts/resizer.ts
	tsc --removeComments --noImplicitAny --out $@ $<

styles/ol.css:	styles/ol.less
	lessc --global-var='chinese=false' $< > $@

styles/ol_zh.css:	styles/ol.less
	lessc --global-var='chinese=true' $< > $@

clean: cleanugly
	rm -f $(ALL_TARGETS)

ALL_UGLY = db/ETCBC4.db.json       db/ETCBC4-translit.db.json  db/nestle1904.db.json \
	   db/ETCBC4.typeinfo.json db/nestle1904.typeinfo.json

PHP = php

allugly: $(ALL_UGLY)

db/%.json: db/%.pretty.json
	$(PHP) json_pretty_print.php -u $< > $@


cleanugly:
	rm -f $(ALL_UGLY)

#TAGS:
# 	find CodeIgniter myapp -name '*.php' -o -name '*.inc' | etags -l php -

docs:
	cd myapp; doxygen


TAGS:
	find myapp -name '*.php' | etags --lang=php --regex='/.*\(public\|private\) .*function.*/' -
