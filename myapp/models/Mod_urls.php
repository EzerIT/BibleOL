<?php

class Mod_urls extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->load->database();
    }

    /// Gets Hebrew buttons
    public function get_heb_buttons() {
        return array(
            //     Label                              Sortorder range
            array("&#x05d0;&#x05d1;-&#x05d0;&#x05d9;", "ab","ak"),
            array("&#x05d0;&#x05db;-&#x05d0;&#x05e8;", "ak","au"),
            array("&#x05d0;&#x05e9;-&#x05d1;&#x05e1;", "au","bp"),
            array("&#x05d1;&#x05e2;-&#x05d2;&#x05d6;", "bp","ch"),
            array("&#x05d2;&#x05d7;-&#x05d3;&#x05e7;", "ch","dt"),
            array("&#x05d3;&#x05e8;-&#x05d6;&#x05e7;", "dt","gt"),
            array("&#x05d6;&#x05e8;-&#x05d7;&#x05dc;", "gt","hm"),
            array("&#x05d7;&#x05de;-&#x05d7;&#x05e9;", "hm","hv"),
            array("&#x05d7;&#x05ea;-&#x05d9;&#x05db;", "hv","jl"),
            array("&#x05d9;&#x05dc;-&#x05db;&#x05d1;", "jl","kd"),
            array("&#x05db;&#x05d3;-&#x05dc;&#x05d5;", "kd","lg"),
            array("&#x05dc;&#x05d6;-&#x05de;&#x05d6;", "lg","mh"),
            array("&#x05de;&#x05d7;-&#x05de;&#x05e1;", "mh","mp"),
            array("&#x05de;&#x05e2;-&#x05de;&#x05e9;", "mp","mv"),
            array("&#x05de;&#x05ea;-&#x05e0;&#x05e2;", "mv","nq"),
            array("&#x05e0;&#x05e4;-&#x05e1;&#x05e2;", "nq","oq"),
            array("&#x05e1;&#x05e4;-&#x05e2;&#x05db;", "oq","pl"),
            array("&#x05e2;&#x05dc;-&#x05e2;&#x05e9;", "pl","pv"),
            array("&#x05e2;&#x05ea;-&#x05e4;&#x05e8;", "pv","qu"),
            array("&#x05e4;&#x05e9;-&#x05e7;&#x05d0;", "qu","sb"),
            array("&#x05e7;&#x05d1;-&#x05e7;&#x05e9;", "sb","ta"),
            array("&#x05e8;&#x05d0;-&#x05e8;&#x05e4;", "ta","tr"),
            array("&#x05e8;&#x05e6;-&#x05e9;&#x05d7;", "tr","ui"),
            array("&#x05e9;&#x05d8;-&#x05e9;&#x05e2;", "ui","uq"),
            array("&#x05e9;&#x05e4;-&#x05ea;&#x05de;", "uq","vn"),
            array("&#x05ea;&#x05e0;-&#x05ea;&#x05e9;", "vn","zz"),
            );
    }

    public function get_heb_buttons_long() {
        return array(
            //     Label                                              Sortorder range
            array('&#x05d0;&#x05d1;-&#x05d0;&#x05d1;&#x05d9;',        'ab', 'abk'),
            array('&#x05d0;&#x05d1;&#x05db;-&#x05d0;&#x05d2;&#x05de;','abk','acn'),
            array('&#x05d0;&#x05d2;&#x05e0;-&#x05d0;&#x05d3;&#x05e8;','acn','adu'),
            array('&#x05d0;&#x05d3;&#x05e9;-&#x05d0;&#x05d5;&#x05e0;','adu','afq'),
            array('&#x05d0;&#x05d5;&#x05e4;-&#x05d0;&#x05d7;',        'afq','aha'),
            array('&#x05d0;&#x05d7;&#x05d0;-&#x05d0;&#x05d7;&#x05d9;','aha','ahl'),
            array('&#x05d0;&#x05d7;&#x05dc;-&#x05d0;&#x05d9;&#x05dc;','ahl','ajm'),
            array('&#x05d0;&#x05d9;&#x05de;-&#x05d0;&#x05dc;&#x05d0;','ajm','alc'),
            array('&#x05d0;&#x05dc;&#x05d2;-&#x05d0;&#x05dc;&#x05d9;','alc','all'),
            array('&#x05d0;&#x05dc;&#x05dc;-&#x05d0;&#x05de;&#x05d5;','all','amj'),
            array('&#x05d0;&#x05de;&#x05d9;-&#x05d0;&#x05de;&#x05ea;','amj','an' ),
            array('&#x05d0;&#x05e0;-&#x05d0;&#x05e1;&#x05e4;',        'an', 'aot'),
            array('&#x05d0;&#x05e1;&#x05e8;-&#x05d0;&#x05e6;&#x05d9;','aot','arl'),
            array('&#x05d0;&#x05e6;&#x05dc;-&#x05d0;&#x05e8;&#x05d5;','arl','atg'),
            array('&#x05d0;&#x05e8;&#x05d6;-&#x05d0;&#x05e8;&#x05e9;','atg','atv'),
            array('&#x05d0;&#x05e8;&#x05ea;-&#x05d0;&#x05e9;&#x05e7;','atv','aut'),
            array('&#x05d0;&#x05e9;&#x05e8;-&#x05d1;&#x05d0;&#x05e8;','aut','bau'),
            array('&#x05d1;&#x05d0;&#x05e9;-&#x05d1;&#x05d4;&#x05e8;','bau','bfa'),
            array('&#x05d1;&#x05d5;&#x05d0;-&#x05d1;&#x05d7;&#x05d5;','bfa','bhj'),
            array('&#x05d1;&#x05d7;&#x05d9;-&#x05d1;&#x05d9;&#x05ea;','bhj','bka'),
            array('&#x05d1;&#x05db;&#x05d0;-&#x05d1;&#x05dc;&#x05e2;','bka','bls'),
            array('&#x05d1;&#x05dc;&#x05e7;-&#x05d1;&#x05e2;&#x05dc;','bls','bpn'),
            array('&#x05d1;&#x05e2;&#x05e0;-&#x05d1;&#x05e7;&#x05d9;','bpn','bsp'),
            array('&#x05d1;&#x05e7;&#x05e2;-&#x05d1;&#x05e8;&#x05d6;','bsp','bth'),
            array('&#x05d1;&#x05e8;&#x05d7;-&#x05d1;&#x05e9;&#x05dc;','bth','bum'),
            array('&#x05d1;&#x05e9;&#x05de;-&#x05d2;&#x05d1;&#x05d4;','bum','cbf'),
            array('&#x05d2;&#x05d1;&#x05d5;-&#x05d2;&#x05d3;&#x05d5;','cbf','cdj'),
            array('&#x05d2;&#x05d3;&#x05d9;-&#x05d2;&#x05d5;&#x05d4;','cdj','cfg'),
            array('&#x05d2;&#x05d5;&#x05d6;-&#x05d2;&#x05d7;&#x05d5;','cfg','chl'),
            array('&#x05d2;&#x05d7;&#x05dc;-&#x05d2;&#x05dc;&#x05d9;','chl','cll'),
            array('&#x05d2;&#x05dc;&#x05dc;-&#x05d2;&#x05e0;&#x05e0;','cll','cnv'),
            array('&#x05d2;&#x05e0;&#x05ea;-&#x05d2;&#x05e8;&#x05e2;','cnv','ctq'),
            array('&#x05d2;&#x05e8;&#x05e4;-&#x05d3;&#x05d1;&#x05d9;','ctq','dbl'),
            array('&#x05d3;&#x05d1;&#x05dc;-&#x05d3;&#x05d5;&#x05d2;','dbl','dfd'),
            array('&#x05d3;&#x05d5;&#x05d3;-&#x05d3;&#x05d9;&#x05de;','dfd','djn'),
            array('&#x05d3;&#x05d9;&#x05e0;-&#x05d3;&#x05de;&#x05d4;','djn','dmf'),
            array('&#x05d3;&#x05de;&#x05d5;-&#x05d3;&#x05e8;&#x05d5;','dmf','dtj'),
            array('&#x05d3;&#x05e8;&#x05d9;-&#x05d4;&#x05d3;&#x05d3;','dtj','ede'),
            array('&#x05d4;&#x05d3;&#x05d4;-&#x05d4;&#x05d9;&#x05d3;','ede','eje'),
            array('&#x05d4;&#x05d9;&#x05d4;-&#x05d4;&#x05e0;&#x05d4;','eje','enh'),
            array('&#x05d4;&#x05e0;&#x05d7;-&#x05d4;&#x05e9;&#x05de;','enh','evf'),
            array('&#x05d4;&#x05ea;&#x05d5;-&#x05d6;&#x05d4;&#x05e8;','evf','gf' ),
            array('&#x05d6;&#x05d5;-&#x05d6;&#x05db;&#x05e8;',        'gf', 'glg'),
            array('&#x05d6;&#x05dc;&#x05d6;-&#x05d6;&#x05e2;&#x05de;','glg','gpq'),
            array('&#x05d6;&#x05e2;&#x05e4;-&#x05d6;&#x05e8;&#x05e2;','gpq','gts'),
            array('&#x05d6;&#x05e8;&#x05e7;-&#x05d7;&#x05d1;&#x05e8;','gts','hbu'),
            array('&#x05d7;&#x05d1;&#x05e9;-&#x05d7;&#x05d5;&#x05d2;','hbu','hfd'),
            array('&#x05d7;&#x05d5;&#x05d3;-&#x05d7;&#x05d6;&#x05d9;','hfd','hgs'),
            array('&#x05d7;&#x05d6;&#x05e7;-&#x05d7;&#x05d9;&#x05dc;','hgs','hjn'),
            array('&#x05d7;&#x05d9;&#x05e0;-&#x05d7;&#x05dc;&#x05d5;','hjn','hlh'),
            array('&#x05d7;&#x05dc;&#x05d7;-&#x05d7;&#x05dc;&#x05e7;','hlh','hlu'),
            array('&#x05d7;&#x05dc;&#x05e9;-&#x05d7;&#x05de;&#x05e7;','hlu','hmt'),
            array('&#x05d7;&#x05de;&#x05e8;-&#x05d7;&#x05e0;&#x05e0;','hmt','hno'),
            array('&#x05d7;&#x05e0;&#x05e1;-&#x05d7;&#x05e4;&#x05e0;','hno','hqq'),
            array('&#x05d7;&#x05e4;&#x05e4;-&#x05d7;&#x05e6;&#x05e8;','hqq','hs' ),
            array('&#x05d7;&#x05e7;-&#x05d7;&#x05e8;&#x05d4;',        'hs', 'htf'),
            array('&#x05d7;&#x05e8;&#x05d5;-&#x05d7;&#x05e8;&#x05e4;','htf','htr'),
            array('&#x05d7;&#x05e8;&#x05e6;-&#x05d7;&#x05e9;&#x05d5;','htr','huk'),
            array('&#x05d7;&#x05e9;&#x05db;-&#x05d7;&#x05ea;&#x05e0;','huk','hvq'),
            array('&#x05d7;&#x05ea;&#x05e4;-&#x05d8;&#x05d5;&#x05d7;','hvq','ifi'),
            array('&#x05d8;&#x05d5;&#x05d8;-&#x05d8;&#x05e4;&#x05d7;','ifi','iql'),
            array('&#x05d8;&#x05e4;&#x05dc;-&#x05d9;&#x05d1;&#x05e0;','iql','jbs'),
            array('&#x05d9;&#x05d1;&#x05e7;-&#x05d9;&#x05d3;&#x05d9;','jbs','jdl'),
            array('&#x05d9;&#x05d3;&#x05dc;-&#x05d9;&#x05d5;&#x05d0;','jdl','jfb'),
            array('&#x05d9;&#x05d5;&#x05d1;-&#x05d9;&#x05d6;&#x05d9;','jfb','jgl'),
            array('&#x05d9;&#x05d6;&#x05dc;-&#x05d9;&#x05d9;&#x05e0;','jgl','jkf'),
            array('&#x05d9;&#x05db;&#x05d5;-&#x05d9;&#x05e0;&#x05d4;','jkf','jnf'),
            array('&#x05d9;&#x05e0;&#x05d5;-&#x05d9;&#x05e2;&#x05dc;','jnf','jpn'),
            array('&#x05d9;&#x05e2;&#x05e0;-&#x05d9;&#x05e6;&#x05d2;','jpn','jre'),
            array('&#x05d9;&#x05e6;&#x05d4;-&#x05d9;&#x05e7;&#x05e8;','jre','jsu'),
            array('&#x05d9;&#x05e7;&#x05e9;-&#x05d9;&#x05e8;&#x05db;','jsu','jtm'),
            array('&#x05d9;&#x05e8;&#x05de;-&#x05d9;&#x05e9;&#x05d9;','jtm','jum'),
            array('&#x05d9;&#x05e9;&#x05de;-&#x05d9;&#x05ea;&#x05e8;','jum','jvv'),
            array('&#x05d9;&#x05ea;&#x05ea;-&#x05db;&#x05d4;&#x05d4;','jvv','ken'),
            array('&#x05db;&#x05d4;&#x05e0;-&#x05db;&#x05d7;&#x05e9;','ken','kj' ),
            array('&#x05db;&#x05d9;-&#x05db;&#x05dc;&#x05de;',        'kj', 'kln'),
            array('&#x05db;&#x05dc;&#x05e0;-&#x05db;&#x05e1;&#x05d5;','kln','koh'),
            array('&#x05db;&#x05e1;&#x05d7;-&#x05db;&#x05e4;&#x05e8;','koh','kqu'),
            array('&#x05db;&#x05e4;&#x05e9;-&#x05db;&#x05e8;&#x05ea;','kqu','kub'),
            array('&#x05db;&#x05e9;&#x05d1;-&#x05dc;&#x05d0;&#x05d4;','kub','lal'),
            array('&#x05dc;&#x05d0;&#x05dc;-&#x05dc;&#x05d4;&#x05d8;','lal','lem'),
            array('&#x05dc;&#x05d4;&#x05de;-&#x05dc;&#x05d7;&#x05de;','lem','lhr'),
            array('&#x05dc;&#x05d7;&#x05e6;-&#x05dc;&#x05e2;&#x05d2;','lhr','lpd'),
            array('&#x05dc;&#x05e2;&#x05d3;-&#x05de;&#x05d0;&#x05d6;','lpd','mak'),
            array('&#x05de;&#x05d0;&#x05db;-&#x05de;&#x05d2;&#x05d3;','mak','mcf'),
            array('&#x05de;&#x05d2;&#x05d5;-&#x05de;&#x05d3;&#x05d9;','mcf','mdk'),
            array('&#x05de;&#x05d3;&#x05db;-&#x05de;&#x05d5;&#x05d8;','mdk','mfk'),
            array('&#x05de;&#x05d5;&#x05db;-&#x05de;&#x05d5;&#x05e8;','mfk','mfu'),
            array('&#x05de;&#x05d5;&#x05e9;-&#x05de;&#x05d7;&#x05d4;','mfu','mhf'),
            array('&#x05de;&#x05d7;&#x05d5;-&#x05de;&#x05d7;&#x05e7;','mhf','mht'),
            array('&#x05de;&#x05d7;&#x05e8;-&#x05de;&#x05d9;&#x05e4;','mht','mjr'),
            array('&#x05de;&#x05d9;&#x05e6;-&#x05de;&#x05db;&#x05e8;','mjr','mku'),
            array('&#x05de;&#x05db;&#x05e9;-&#x05de;&#x05dc;&#x05d9;','mku','mlk'),
            array('&#x05de;&#x05dc;&#x05db;-&#x05de;&#x05de;&#x05e1;','mlk','mmt'),
            array('&#x05de;&#x05de;&#x05e8;-&#x05de;&#x05e0;&#x05e2;','mmt','mns'),
            array('&#x05de;&#x05e0;&#x05e7;-&#x05de;&#x05e1;&#x05e8;','mns','mov'),
            array('&#x05de;&#x05e1;&#x05ea;-&#x05de;&#x05e2;&#x05dc;','mov','mpm'),
            array('&#x05de;&#x05e2;&#x05de;-&#x05de;&#x05e4;&#x05dc;','mpm','mqp'),
            array('&#x05de;&#x05e4;&#x05e2;-&#x05de;&#x05e6;&#x05e2;','mqp','mrq'),
            array('&#x05de;&#x05e6;&#x05e4;-&#x05de;&#x05e8;',        'mrq','mta'),
            array('&#x05de;&#x05e8;&#x05d0;-&#x05de;&#x05e8;&#x05d8;','mta','mtj'),
            array('&#x05de;&#x05e8;&#x05d9;-&#x05de;&#x05e8;&#x05e8;','mtj','mtu'),
            array('&#x05de;&#x05e8;&#x05e9;-&#x05de;&#x05e9;&#x05d7;','mtu','mui'),
            array('&#x05de;&#x05e9;&#x05d8;-&#x05de;&#x05e9;&#x05e0;','mui','muo'),
            array('&#x05de;&#x05e9;&#x05e1;-&#x05de;&#x05ea;&#x05e0;','muo','mvs'),
            array('&#x05de;&#x05ea;&#x05e7;-&#x05e0;&#x05d1;&#x05d8;','mvs','nbj'),
            array('&#x05e0;&#x05d1;&#x05d9;-&#x05e0;&#x05d2;&#x05e9;','nbj','nd' ),
            array('&#x05e0;&#x05d3;-&#x05e0;&#x05d5;&#x05d1;',        'nd', 'nfd'),
            array('&#x05e0;&#x05d5;&#x05d3;-&#x05e0;&#x05d7;&#x05d4;','nfd','nhf'),
            array('&#x05e0;&#x05d7;&#x05d5;-&#x05e0;&#x05d7;&#x05ea;','nhf','nie'),
            array('&#x05e0;&#x05d8;&#x05d4;-&#x05e0;&#x05db;&#x05d4;','nie','nkf'),
            array('&#x05e0;&#x05db;&#x05d5;-&#x05e0;&#x05e2;&#x05d4;','nkf','npf'),
            array('&#x05e0;&#x05e2;&#x05d5;-&#x05e0;&#x05e4;&#x05e6;','npf','nqu'),
            array('&#x05e0;&#x05e4;&#x05e9;-&#x05e0;&#x05e7;&#x05d5;','nqu','nsj'),
            array('&#x05e0;&#x05e7;&#x05d9;-&#x05e0;&#x05e9;&#x05d9;','nsj','nuk'),
            array('&#x05e0;&#x05e9;&#x05db;-&#x05e0;&#x05ea;&#x05e7;','nuk','nvt'),
            array('&#x05e0;&#x05ea;&#x05e8;-&#x05e1;&#x05d2;&#x05e8;','nvt','od' ),
            array('&#x05e1;&#x05d3;-&#x05e1;&#x05d7;&#x05e8;',        'od', 'oij'),
            array('&#x05e1;&#x05d8;&#x05d9;-&#x05e1;&#x05dc;&#x05d5;','oij','olh'),
            array('&#x05e1;&#x05dc;&#x05d7;-&#x05e1;&#x05e2;&#x05e4;','olh','opt'),
            array('&#x05e1;&#x05e2;&#x05e8;-&#x05e1;&#x05e8;&#x05d1;','opt','otc'),
            array('&#x05e1;&#x05e8;&#x05d2;-&#x05e2;&#x05d1;&#x05d3;','otc','pbe'),
            array('&#x05e2;&#x05d1;&#x05d4;-&#x05e2;&#x05d3;&#x05d4;','pbe','pdf'),
            array('&#x05e2;&#x05d3;&#x05d5;-&#x05e2;&#x05d5;&#x05d3;','pdf','pfe'),
            array('&#x05e2;&#x05d5;&#x05d4;-&#x05e2;&#x05d5;&#x05ea;','pfe','pg' ),
            array('&#x05e2;&#x05d6;-&#x05e2;&#x05d6;&#x05e8;',        'pg', 'pgv'),
            array('&#x05e2;&#x05d6;&#x05ea;-&#x05e2;&#x05d9;&#x05e0;','pgv','pjq'),
            array('&#x05e2;&#x05d9;&#x05e4;-&#x05e2;&#x05dc;&#x05d5;','pjq','plg'),
            array('&#x05e2;&#x05dc;&#x05d6;-&#x05e2;&#x05de;&#x05d5;','plg','pmj'),
            array('&#x05e2;&#x05de;&#x05d9;-&#x05e2;&#x05e0;&#x05d4;','pmj','pnf'),
            array('&#x05e2;&#x05e0;&#x05d5;-&#x05e2;&#x05e4;&#x05e8;','pnf','pr' ),
            array('&#x05e2;&#x05e6;-&#x05e2;&#x05e7;&#x05d1;',        'pr', 'psd'),
            array('&#x05e2;&#x05e7;&#x05d3;-&#x05e2;&#x05e8;&#x05d3;','psd','pte'),
            array('&#x05e2;&#x05e8;&#x05d4;-&#x05e2;&#x05e8;&#x05e8;','pte','ptu'),
            array('&#x05e2;&#x05e8;&#x05e9;-&#x05e2;&#x05e9;&#x05ea;','ptu','pv' ),
            array('&#x05e2;&#x05ea;-&#x05e4;&#x05d2;&#x05e9;',        'pv', 'qde'),
            array('&#x05e4;&#x05d3;&#x05d4;-&#x05e4;&#x05d5;&#x05e8;','qde','qfu'),
            array('&#x05e4;&#x05d5;&#x05e9;-&#x05e4;&#x05db;&#x05e8;','qfu','qla'),
            array('&#x05e4;&#x05dc;&#x05d0;-&#x05e4;&#x05dc;&#x05e0;','qla','qlo'),
            array('&#x05e4;&#x05dc;&#x05e1;-&#x05e4;&#x05e2;&#x05dc;','qlo','qpm'),
            array('&#x05e4;&#x05e2;&#x05de;-&#x05e4;&#x05e8;&#x05d3;','qpm','qte'),
            array('&#x05e4;&#x05e8;&#x05d4;-&#x05e4;&#x05e8;&#x05e6;','qte','qts'),
            array('&#x05e4;&#x05e8;&#x05e7;-&#x05e4;&#x05ea;&#x05d4;','qts','qvf'),
            array('&#x05e4;&#x05ea;&#x05d5;-&#x05e6;&#x05d1;&#x05d4;','qvf','rbf'),
            array('&#x05e6;&#x05d1;&#x05d5;-&#x05e6;&#x05d5;&#x05de;','rbf','rfp'),
            array('&#x05e6;&#x05d5;&#x05e2;-&#x05e6;&#x05d9;&#x05d4;','rfp','rjf'),
            array('&#x05e6;&#x05d9;&#x05d5;-&#x05e6;&#x05de;&#x05d0;','rjf','rmd'),
            array('&#x05e6;&#x05de;&#x05d3;-&#x05e6;&#x05e2;&#x05d9;','rmd','rpn'),
            array('&#x05e6;&#x05e2;&#x05e0;-&#x05e6;&#x05e4;&#x05e8;','rpn','rqv'),
            array('&#x05e6;&#x05e4;&#x05ea;-&#x05e7;&#x05d0;',        'rqv','sav'),
            array('&#x05e7;&#x05d0;&#x05ea;-&#x05e7;&#x05d3;&#x05e8;','sav','sdu'),
            array('&#x05e7;&#x05d3;&#x05e9;-&#x05e7;&#x05d5;&#x05e6;','sdu','sft'),
            array('&#x05e7;&#x05d5;&#x05e8;-&#x05e7;&#x05d9;&#x05e7;','sft','sjt'),
            array('&#x05e7;&#x05d9;&#x05e8;-&#x05e7;&#x05de;&#x05dc;','sjt','smr'),
            array('&#x05e7;&#x05de;&#x05e6;-&#x05e7;&#x05e6;&#x05d5;','smr','srh'),
            array('&#x05e7;&#x05e6;&#x05d7;-&#x05e7;&#x05e8;&#x05d4;','srh','stf'),
            array('&#x05e7;&#x05e8;&#x05d5;-&#x05e7;&#x05e8;&#x05ea;','stf','su' ),
            array('&#x05e7;&#x05e9;-&#x05e8;&#x05d0;&#x05de;',        'su', 'tau'),
            array('&#x05e8;&#x05d0;&#x05e9;-&#x05e8;&#x05d2;&#x05d6;','tau','tcl'),
            array('&#x05e8;&#x05d2;&#x05dc;-&#x05e8;&#x05d5;&#x05d7;','tcl','tfj'),
            array('&#x05e8;&#x05d5;&#x05d9;-&#x05e8;&#x05d7;&#x05d5;','tfj','thj'),
            array('&#x05e8;&#x05d7;&#x05d9;-&#x05e8;&#x05d9;&#x05e9;','thj','tk' ),
            array('&#x05e8;&#x05db;-&#x05e8;&#x05de;&#x05e1;',        'tk', 'tmu'),
            array('&#x05e8;&#x05de;&#x05e9;-&#x05e8;&#x05e2;&#x05dc;','tmu','tpm'),
            array('&#x05e8;&#x05e2;&#x05de;-&#x05e8;&#x05e4;&#x05d9;','tpm','tqo'),
            array('&#x05e8;&#x05e4;&#x05e1;-&#x05e8;&#x05e7;&#x05d7;','tqo','tsj'),
            array('&#x05e8;&#x05e7;&#x05d9;-&#x05e9;&#x05d0;&#x05d5;','tsj','uai'),
            array('&#x05e9;&#x05d0;&#x05d8;-&#x05e9;&#x05d1;&#x05d9;','uai','ubk'),
            array('&#x05e9;&#x05d1;&#x05db;-&#x05e9;&#x05d1;&#x05ea;','ubk','uca'),
            array('&#x05e9;&#x05d2;&#x05d0;-&#x05e9;&#x05d4;&#x05de;','uca','uet'),
            array('&#x05e9;&#x05d4;&#x05e8;-&#x05e9;&#x05d5;&#x05dc;','uet','ufm'),
            array('&#x05e9;&#x05d5;&#x05de;-&#x05e9;&#x05d7;&#x05d3;','ufm','uhe'),
            array('&#x05e9;&#x05d7;&#x05d4;-&#x05e9;&#x05d8;&#x05d7;','uhe','uii'),
            array('&#x05e9;&#x05d8;&#x05d8;-&#x05e9;&#x05d9;&#x05e9;','uii','ujv'),
            array('&#x05e9;&#x05d9;&#x05ea;-&#x05e9;&#x05db;&#x05e0;','ujv','ukt'),
            array('&#x05e9;&#x05db;&#x05e8;-&#x05e9;&#x05dc;&#x05d7;','ukt','uli'),
            array('&#x05e9;&#x05dc;&#x05d8;-&#x05e9;&#x05dc;&#x05de;','uli','uln'),
            array('&#x05e9;&#x05dc;&#x05e0;-&#x05e9;&#x05de;&#x05d9;','uln','uml'),
            array('&#x05e9;&#x05de;&#x05dc;-&#x05e9;&#x05de;&#x05e8;','uml','umu'),
            array('&#x05e9;&#x05de;&#x05e9;-&#x05e9;&#x05e2;&#x05d9;','umu','upl'),
            array('&#x05e9;&#x05e2;&#x05dc;-&#x05e9;&#x05e4;&#x05d8;','upl','uqj'),
            array('&#x05e9;&#x05e4;&#x05d9;-&#x05e9;&#x05e7;&#x05d4;','uqj','usf'),
            array('&#x05e9;&#x05e7;&#x05d5;-&#x05e9;&#x05e8;&#x05d5;','usf','uth'),
            array('&#x05e9;&#x05e8;&#x05d7;-&#x05e9;&#x05e9;',        'uth','uua'),
            array('&#x05e9;&#x05e9;&#x05d0;-&#x05ea;&#x05d0;&#x05e8;','uua','vau'),
            array('&#x05ea;&#x05d0;&#x05e9;-&#x05ea;&#x05d5;&#x05dc;','vau','vfp'),
            array('&#x05ea;&#x05d5;&#x05e2;-&#x05ea;&#x05d9;&#x05e8;','vfp','vju'),
            array('&#x05ea;&#x05d9;&#x05e9;-&#x05ea;&#x05de;&#x05d5;','vju','vmh'),
            array('&#x05ea;&#x05de;&#x05d7;-&#x05ea;&#x05e2;&#x05dc;','vmh','vpn'),
            array('&#x05ea;&#x05e2;&#x05e0;-&#x05ea;&#x05e7;&#x05e4;','vpn','vta'),
            array('&#x05ea;&#x05e8;&#x05d0;-&#x05ea;&#x05e9;&#x05e2;','vta','zzz'),
            );
    }

    /// Gets Aramaic buttons
    public function get_aram_buttons() {
        return array(
            //     Label                              Sortorder range
            array("&#x05d0;&#x05d1;-&#x05de;&#x05d5;", "ab","mg"),
            array("&#x05de;&#x05d6;-&#x05ea;&#x05ea;", "mg","zz"),
            );
    }

    public function get_aram_buttons_long() {
        return array(
            //     Label                                              Sortorder range
            array('&#x05d0;&#x05d1;-&#x05d0;&#x05e0;&#x05ea;',        'ab', 'aof'),
            array('&#x05d0;&#x05e1;&#x05d5;-&#x05d1;&#x05d9;&#x05e0;','aof','bjt'),
            array('&#x05d1;&#x05d9;&#x05e8;-&#x05d2;&#x05dc;&#x05dc;','bjt','cmt'),
            array('&#x05d2;&#x05de;&#x05e8;-&#x05d4;&#x05d5;&#x05d0;','cmt','efe'),
            array('&#x05d4;&#x05d5;&#x05d4;-&#x05d7;&#x05d8;&#x05d0;','efe','hj' ),
            array('&#x05d7;&#x05d9;-&#x05d9;&#x05d1;&#x05e9;',        'hj', 'jct'),
            array('&#x05d9;&#x05d2;&#x05e8;-&#x05db;&#x05e0;&#x05e9;','jct','knv'),
            array('&#x05db;&#x05e0;&#x05ea;-&#x05de;&#x05d7;&#x05d0;','knv','mhl'),
            array('&#x05de;&#x05d7;&#x05dc;-&#x05e0;&#x05d3;&#x05e0;','mhl','nef'),
            array('&#x05e0;&#x05d4;&#x05d5;-&#x05e1;&#x05dc;&#x05e7;','nef','opd'),
            array('&#x05e1;&#x05e2;&#x05d3;-&#x05e2;&#x05e0;&#x05e4;','opd','pnu'),
            array('&#x05e2;&#x05e0;&#x05e9;-&#x05e6;&#x05d5;&#x05d0;','pnu','rle'),
            array('&#x05e6;&#x05dc;&#x05d4;-&#x05e8;&#x05d1;&#x05d5;','rle','tbj'),
            array('&#x05e8;&#x05d1;&#x05d9;-&#x05e9;&#x05d5;&#x05d4;','tbj','uft'),
            array('&#x05e9;&#x05d5;&#x05e8;-&#x05e9;&#x05e4;&#x05dc;','uft','uqt'),
            array('&#x05e9;&#x05e4;&#x05e8;-&#x05ea;&#x05ea;&#x05e0;','uqt','zzz'),
            );
    }
    
    /// Gets Greek buttons
    public function get_greek_buttons() {
        return array(
            //     Label   Sortorder range
            array('α-αμ',  'αα','αν'),  
            array('αν-απ', 'αν','αρ'), 
            array('αρ-βω', 'αρ','γα'), 
            array('γ-δω',  'γα','εα'), 
            array('ε-εν',  'εα','εξ'), 
            array('εξ-εω', 'εξ','ζα'), 
            array('ζ-ιω',  'ζα','κα'), 
            array('κ-κο',  'κα','κρ'), 
            array('κρ-μη', 'κρ','μι'), 
            array('μι-οψ', 'μι','πα'), 
            array('π-πν',  'πα','πο'), 
            array('πο-ρω', 'πο','σα'), 
            array('σ-στ',  'σα','συ'), 
            array('συ-σω', 'συ','τα'), 
            array('τ-υψ',  'τα','φα'), 
            array('φ-ωφ',  'φα','ωω'), 
            );
    }

    public function get_greek_buttons_long() {
        return array(
            //     Label    Sortorder range
            array('ααρ-αγν','ααρ','αγο'),
            array('αγο-αηρ','αγο','αθα'),
            array('αθα-αιφ','αθα','αιχ'),
            array('αιχ-αλα','αιχ','αλε'),
            array('αλε-αμα','αλε','αμε'),
            array('αμε-ανα','αμε','ανδ'),
            array('ανδ-ανο','ανδ','αντ'),
            array('αντ-αξι','αντ','αορ'),
            array('αορ-απε','αορ','απι'),
            array('απι-απο','απι','αππ'),
            array('αππ-αρν','αππ','αρο'),
            array('αρο-ασθ','αρο','ασι'),
            array('ασι-ατο','ασι','αττ'),
            array('αττ-αφο','αττ','αφρ'),
            array('αφρ-βαρ','αφρ','βασ'),
            array('βασ-βιθ','βασ','βιο'),
            array('βιο-βρο','βιο','βρυ'),
            array('βρυ-γεν','βρυ','γερ'),
            array('γερ-γυν','γερ','γωγ'),
            array('γωγ-δεν','γωγ','δεξ'),
            array('δεξ-δια','δεξ','διδ'),
            array('διδ-δις','διδ','διυ'),
            array('διυ-δυο','διυ','δυσ'),
            array('δυσ-εγκ','δυσ','εγχ'),
            array('εγχ-εις','εγχ','ειτ'),
            array('ειτ-εκλ','ειτ','εκμ'),
            array('εκμ-ελα','εκμ','ελε'),
            array('ελε-εμμ','ελε','εμο'),
            array('εμο-ενε','εμο','ενθ'),
            array('ενθ-εξα','ενθ','εξε'),
            array('εξε-επα','εξε','επε'),
            array('επε-επι','επε','επο'),
            array('επο-ετα','επο','ετε'),
            array('ετε-ευν','ετε','ευο'),
            array('ευο-εφι','ευο','εφρ'),
            array('εφρ-ηδη','εφρ','ηδο'),
            array('ηδο-θαρ','ηδο','θαυ'),
            array('θαυ-θιγ','θαυ','θλι'),
            array('θλι-ιατ','θλι','ιδι'),
            array('ιδι-ιου','ιδι','ιππ'),
            array('ιππ-καθ','ιππ','και'),
            array('και-καν','και','καπ'),
            array('καπ-κατ','καπ','καυ'),
            array('καυ-κητ','καυ','κηφ'),
            array('κηφ-κνι','κηφ','κοδ'),
            array('κοδ-κορ','κοδ','κοσ'),
            array('κοσ-κτη','κοσ','κτι'),
            array('κτι-λαμ','κτι','λαν'),
            array('λαν-λιμ','λαν','λιν'),
            array('λιν-μαα','λιν','μαγ'),
            array('μαγ-μαρ','μαγ','μασ'),
            array('μασ-μεν','μασ','μερ'),
            array('μερ-μετ','μερ','μεχ'),
            array('μεχ-μνη','μεχ','μογ'),
            array('μογ-ναα','μογ','ναγ'),
            array('ναγ-νικ','ναγ','νιν'),
            array('νιν-ξυλ','νιν','ξυρ'),
            array('ξυρ-οκν','ξυρ','οκτ'),
            array('οκτ-ονι','οκτ','ονο'),
            array('ονο-ορυ','ονο','ορφ'),
            array('ορφ-οφθ','ορφ','οφι'),
            array('οφι-παν','οφι','παρ'),
            array('παρ-παρ','παρ','πας'),
            array('πας-πεν','πας','πεπ'),
            array('πεπ-περ','πεπ','πετ'),
            array('πετ-πλε','πετ','πλη'),
            array('πλη-πολ','πλη','πομ'),
            array('πομ-πρα','πομ','πρε'),
            array('πρε-προ','πρε','πρυ'),
            array('πρυ-πυρ','πρυ','πωλ'),
            array('πωλ-ρου','πωλ','ρυμ'),
            array('ρυμ-σαρ','ρυμ','σατ'),
            array('σατ-σιρ','σατ','σιτ'),
            array('σιτ-σμυ','σιτ','σοδ'),
            array('σοδ-στε','σοδ','στη'),
            array('στη-συγ','στη','συζ'),
            array('συζ-συμ','συζ','συν'),
            array('συν-συν','συν','συρ'),
            array('συρ-σωφ','συρ','ταβ'),
            array('ταβ-τελ','ταβ','τερ'),
            array('τερ-τοι','τερ','τοκ'),
            array('τοκ-τρυ','τοκ','τρω'),
            array('τρω-υπε','τρω','υπη'),
            array('υπη-υστ','υπη','υφα'),
            array('υφα-φια','υφα','φιλ'),
            array('φιλ-φον','φιλ','φορ'),
            array('φορ-φωτ','φορ','χαι'),
            array('χαι-χλι','χαι','χλο'),
            array('χλο-χωρ','χλο','ψαλ'),
            array('ψαλ-ωση','ψαλ','ωσπ'),
            array('ωσπ-ωφε','ωσπ','ωωω'),
            );
    }

    /// Gets Latin buttons
    public function get_latin_buttons() {
        return array(
            //     Label   Sortorder range
            array('aa-ar','aa','as'),
            array('as-cn','as','co'),
            array('co-de','co','di'),
            array('di-fa','di','fe'),
            array('fe-il','fe','im'),
            array('im-lo','im','lu'),
            array('lu-nu','lu','o'),
            array('o-po','o','pr'),
            array('pr-ra','pr','re'),
            array('re-sq','re','st'),
            array('st-us','st','ut'),
            array('ut-zo','ut','zz'),
        );
    }

    public function get_latin_buttons_long() {
        return array(
            //     Label    Sortorder range
            array('aar-aby','aar','acc'),
            array('acc-adi','acc','adm'),
            array('adm-aeq','adm','aer'),
            array('aer-ale','aer','ali'),
            array('ali-ami','ali','amo'),
            array('amo-anu','amo','ape'),
            array('ape-ard','ape','are'),
            array('are-asp','are','ass'),
            array('ass-ava','ass','ave'),
            array('ave-ben','ave','ber'),
            array('ber-cai','ber','cal'),
            array('cal-car','cal','cas'),
            array('cas-cet','cas','cha'),
            array('cha-civ','cha','cla'),
            array('cla-coi','cla','col'),
            array('col','col','com'),
            array('coma-comp','com','con'),
            array('conc-conr','conc','cons'),
            array('cons-cont','cons','conv'),
            array('conv-cou','conv','cra'),
            array('cra-cup','cra','cur'),
            array('cur-deb','cur','dec'),
            array('dec-der','dec','des'),
            array('des-dex','des','dia'),
            array('dia-dir','dia','dis'),
            array('dis-dol','dis','dom'),
            array('dom-edo','dom','edu'),
            array('edu-eo','edu','epa'),
            array('epa-eut','epa','eva'),
            array('eva-exf','eva','exh'),
            array('exh-exs','exh','ext'),
            array('ext-fen','ext','fer'),
            array('fer-flo','fer','flu'),
            array('flu-fri','flu','fro'),
            array('fro-gau','fro','gaz'),
            array('gaz-gom','gaz','gra'),
            array('gra-hic','gra','hie'),
            array('hie-hym','hie','hyp'),
            array('hyp-ili','hyp','ill'),
            array('ill-imm','ill','imp'),
            array('imp-inc','imp','ind'),
            array('ind-ini','ind','inn'),
            array('inn-ins','inn','int'),
            array('int-ioh','int','ion'),
            array('ion-iuc','ion','iud'),
            array('iud-lam','iud','lan'),
            array('lan-lib','lan','lic'),
            array('lic-lug','lic','lum'),
            array('lum-mai','lum','mal'),
            array('mal-mas','mal','mat'),
            array('mat-mes','mat','met'),
            array('met-moe','met','mol'),
            array('mol-mur','mol','mus'),
            array('mus-nec','mus','neg'),
            array('neg-nom','neg','non'),
            array('non-obm','non','obn'),
            array('obn-odo','obn','off'),
            array('off-ort','off','os'),
            array('os-pap','os','par'),
            array('par-pax','par','pec'),
            array('pec-pen','pec','per'),
            array('per-pie','per','pig'),
            array('pig-pop','pig','por'),
            array('por-pot','por','pra'),
            array('pra-pre','pra','pri'),
            array('pri','pri','pro'),
            array('pro','pro','pru'),
            array('pru-pyt','pru','qua'),
            array('qua-que','qua','qui'),
            array('qui-rab','qui','rac'),
            array('rac-ree','rac','ref'),
            array('ref-ren','ref','rep'),
            array('rep-rob','rep','rog'),
            array('rog-sag','rog','sal'),
            array('sal-sap','sal','sar'),
            array('sar-scu','sar','se'),
            array('se-seq','se','ser'),
            array('ser-sil','ser','sim'),
            array('sim-som','sim','son'),
            array('son-squ','son','sta'),
            array('sta-sua','sta','sub'),
            array('sub-sum','sub','sup'),
            array('sup-sym','sup','syn'),
            array('syn-tem','syn','ten'),
            array('ten-tib','ten','tim'),
            array('tim-tre','tim','tri'),
            array('tri-ube','tri','ubi'),
            array('ubi-usu','ubi','ut'),
            array('ut-vel','ut','ven'),
            array('ven-vig','ven','vil'),
            array('vil-voc','vil','vol'),
            array('vol-zor','vol','zzz')
        );
    }

    public function get_glosses(string $language, string $from, string $to) {
        $query = $this->db->from("lexicon_{$language} he")->join("lexicon_{$language}_en en",'en.lex_id=he.id')
            ->where('sortorder >=',$from)
            ->where('sortorder <',$to)
            ->order_by('sortorder,roman')
            ->get();

        $last_lex = '';
        $result = array();
        foreach ($query->result() as $row) {
            // Only take each lexeme once, ignoring vs
            if ($row->lex!==$last_lex) {
                $result[] = $row;
                $last_lex = $row->lex;
            }
        }
        return $result;
    }

    public function get_frequent_glosses(string $language, int $gloss_count) {
        $query = $this->db->from("lexicon_{$language} he")->join("lexicon_{$language}_en en",'en.lex_id=he.id')
            ->order_by('tally','DESC')
            ->limit(2*$gloss_count)
            ->get();

        $last_lex = '';
        $result = array();
        $tally = 0;
        foreach ($query->result() as $row) {
            // Only take each lexeme once, ignoring vs
            if ($row->lex!==$last_lex) {
                if (count($result)>=$gloss_count) {
                    // Break when we have a row with a smaller tally
                    if ($tally > $row->tally)
                        break;
                }
                $result[] = $row;
                $last_lex = $row->lex;
                if (count($result)==$gloss_count)
                    $tally = $row->tally;
            }
        }
        assert(count($result)>=$gloss_count); // To ensure that the SQL LIMIT is high enough

        return $result;
    }

    public function get_heb_urls(string $language, array &$words) {
        foreach ($words as &$w) {
            $query = $this->db->where('lex',$w->lex)->where('language',$language)->get('heb_urls');
            if ($query->num_rows()>0)
                $w->urls = $query->result();
        }
    }

    // Checks if the specified $url is live
    private function check_url($url) {
        $headers = @get_headers($url,true);

        if (!$headers)
            return false;
    
        // Find last HTTP response:
        for ($i=0; isset($headers[$i]); ++$i)
            /* Nothing */;

        $http_resp = explode(' ', $headers[$i-1]);
        return $http_resp[1]=='200';
    }
    
    private function get_bad_urls(string $sql_table, string $field) {
        $badurls = array();
        
        $query = $this->db->get($sql_table);
        foreach ($query->result() as $row) {
            if (!$this->check_url($row->$field))
                $badurls[] = $row;
        }
        return $badurls;
    }
        
    public function get_bad_heb_urls() {
        return $this->get_bad_urls('heb_urls','url');
    }

    public function get_bad_bible_urls() {
        return $this->get_bad_urls('bible_urls','url');
    }


    public function set_heb_url(int $id, string $link, string $icon) {
        $this->db->where('id',$id)->update('heb_urls',array('url' => $link,
                                                            'icon' => $icon));
    }

    public function create_heb_url(string $lex, string $language, string $link, string $icon) {
        $this->db->insert('heb_urls',array('lex' => $lex,
                                           'language' => $language,
                                           'url' => $link,
                                           'icon' => $icon));
    }

    public function delete_heb_url(int $id) {
        $this->db->where('id',$id)->delete('heb_urls');
    }
  }
