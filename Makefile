ALL_TARGETS = js/ol.js js/fontselector.js js/editquiz.js styles/ol.css styles/ol_zh.css

all:	$(ALL_TARGETS) allugly

js/ol.js:	ts/ol.ts ts/answer.ts ts/charset.ts ts/componentwithyesno.ts ts/configuration.ts ts/dictionary.ts \
	ts/sentencegrammar.ts ts/displaymonadobject.ts \
	ts/jquery/jquery.d.ts ts/jqueryui/jqueryui.d.ts ts/localization.ts ts/monadobject.ts ts/panelquestion.ts \
	ts/statistics.ts ts/stringwithsort.ts ts/util.ts ts/quiz.ts ts/quizdata.ts ts/resizer.ts \
	ts/bootstrap/bootstrap.d.ts ts/localization_general.ts
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<

js/editquiz.js:	ts/editquiz.ts ts/jquery/jquery.d.ts ts/configuration.ts ts/localization.ts \
	ts/paneltemplmql.ts ts/paneltemplsentenceselector.ts ts/paneltemplquizobjectselector.ts \
	ts/paneltemplquizfeatures.ts ts/sentencegrammar.ts \
	ts/stringwithsort.ts ts/sortingcheckbox.ts ts/util.ts ts/verbclasspanel.ts ts/resizer.ts \
	ts/bootstrap/bootstrap.d.ts ts/jqueryui/jqueryui.d.ts ts/charset.ts ts/localization_general.ts
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<


js/fontselector.js:	ts/fontselector.ts ts/fontdetect.d.ts ts/jquery/jquery.d.ts ts/util.ts ts/localization_general.ts
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<

styles/ol.css:	styles/ol.less
	lessc $< > $@

styles/ol_zh.css:	styles/ol_zh.less
	lessc $< > $@

clean: cleanugly
	rm -f $(ALL_TARGETS)

ALL_UGLY = db/ETCBC4.da.prop.json      db/ETCBC4-translit.da.prop.json      db/nestle1904.da.prop.json      \
           db/ETCBC4.zh-simp.prop.json db/ETCBC4-translit.zh-simp.prop.json db/nestle1904.zh-simp.prop.json \
           db/ETCBC4.zh-trad.prop.json db/ETCBC4-translit.zh-trad.prop.json db/nestle1904.zh-trad.prop.json \
           db/ETCBC4.pt.prop.json      db/ETCBC4-translit.pt.prop.json	    db/nestle1904.pt.prop.json	    \
	   db/ETCBC4.es.prop.json      db/ETCBC4-translit.es.prop.json	    db/nestle1904.es.prop.json	    \
	   db/ETCBC4.en.prop.json      db/ETCBC4-translit.en.prop.json	    db/nestle1904.en.prop.json	    \
           db/ETCBC4.db.json           db/ETCBC4-translit.db.json           db/nestle1904.db.json           \
           db/ETCBC4.typeinfo.json                                          db/nestle1904.typeinfo.json

PHP = php

allugly: $(ALL_UGLY)

db/%.json: db/%.pretty.json
	$(PHP) json_pretty_print.php -u $< > $@


cleanugly:
	rm -f $(ALL_UGLY)

TAGS:
	find CodeIgniter myapp -name '*.php' -o -name '*.inc' | etags -l php -

docs:
	cd myapp; doxygen

