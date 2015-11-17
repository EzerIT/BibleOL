ALL_TARGETS = testversion js/ol.js js/fontselector.js js/editquiz.js styles/ol.css

all:	$(ALL_TARGETS)

testversion:
	@tsc -v | grep 'Version 1.6.2' > /dev/null || { echo Wrong tsc version ; exit 255; }

js/ol.js:	ts/ol.ts ts/answer.ts ts/charset.ts ts/componentwithyesno.ts ts/configuration.ts ts/dictionary.ts \
	ts/sentencegrammar.ts ts/displaymonadobject.ts \
	ts/jquery/jquery.d.ts ts/jqueryui/jqueryui.d.ts ts/localization.ts ts/monadobject.ts ts/panelquestion.ts \
	ts/statistics.ts ts/stringwithsort.ts ts/util.ts ts/quiz.ts ts/quizdata.ts 
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<

js/editquiz.js:	ts/editquiz.ts ts/jquery/jquery.d.ts ts/configuration.ts ts/localization.ts \
	ts/paneltemplmql.ts ts/paneltemplsentenceselector.ts ts/paneltemplquizobjectselector.ts \
	ts/paneltemplquizfeatures.ts ts/sentencegrammar.ts \
	ts/stringwithsort.ts ts/sortingcheckbox.ts ts/util.ts ts/verbclasspanel.ts
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<



js/fontselector.js:	ts/fontselector.ts ts/fontdetect.d.ts
	@#tsc --noImplicitAny --out $@ $<
	tsc --out $@ $<

styles/ol.css:	styles/ol.less
	lessc $< > $@

clean:
	rm -f $(ALL_TARGETS)

ALL_JSON = db/Tisch.en.prop.json \
	db/WIVU.en.prop.json db/WIVU-a.en.prop.json db/WIVU-b.en.prop.json \
	db/WIVU_part2.en.prop.json db/WIVU_part2-a.en.prop.json db/WIVU_part2-b.en.prop.json \
	db/Tisch.db.json \
	db/WIVU.db.json db/WIVU-a.db.json db/WIVU-b.db.json \
	db/WIVU_part2.db.json db/WIVU_part2-a.db.json db/WIVU_part2-b.db.json \
	db/Tisch.typeinfo.json db/WIVU.typeinfo.json db/WIVU_part2.typeinfo.json
DB_SRC = ../../EclipseWorkspace/3ET.git/db/
PHP = c:/xampp/php/php

alljson: $(ALL_JSON)

db/Tisch.en.prop.json: $(DB_SRC)Tisch.properties
	$(PHP) prop2json.php $< > $@

db/WIVU.en.prop.json: $(DB_SRC)WIVU.properties
	$(PHP) prop2json.php $< > $@

db/WIVU-a.en.prop.json: $(DB_SRC)WIVU-a.properties
	$(PHP) prop2json.php $< > $@

db/WIVU-b.en.prop.json: $(DB_SRC)WIVU-b.properties
	$(PHP) prop2json.php $< > $@

db/WIVU_part2.en.prop.json: $(DB_SRC)WIVU_part2.properties
	$(PHP) prop2json.php $< > $@

db/WIVU_part2-a.en.prop.json: $(DB_SRC)WIVU_part2-a.properties
	$(PHP) prop2json.php $< > $@

db/WIVU_part2-b.en.prop.json: $(DB_SRC)WIVU_part2-b.properties
	$(PHP) prop2json.php $< > $@

db/Tisch.json: $(DB_SRC)4_Tisch.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU.db.json: $(DB_SRC)1_WIVU.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU-a.db.json: $(DB_SRC)1a_WIVU.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU-b.db.json: $(DB_SRC)1b_WIVU.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU_part2.db.json: $(DB_SRC)2_WIVU_part2.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU_part2-a.db.json: $(DB_SRC)2a_WIVU_part2.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/WIVU_part2-b.db.json: $(DB_SRC)2b_WIVU_part2.dbxml
	$(PHP) dbxml2json.php  $< > $@

db/Tisch.typeinfo.json: db/Tisch
	$(PHP) maketypeinfo.php $< > $@

db/WIVU.typeinfo.json: db/WIVU
	$(PHP) maketypeinfo.php $< > $@

db/WIVU_part2.typeinfo.json: db/WIVU_part2
	$(PHP) maketypeinfo.php $< > $@


cleanjson:
	rm -f $(ALL_JSON)

TAGS:
	find CodeIgniter myapp -name '*.php' -o -name '*.inc' | etags -l php -

docs:
	cd myapp; doxygen

