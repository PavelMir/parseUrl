<?php
$countriesStr=".ac.ad.ae.af.ag.ai.al.am.ao.aq.ar.as.at.au.aw.ax.az.ba.bb.bd.be.bf.bg.bh.bi.bj.bm.bn.bo.br.bs.bt.bw.by.bz.ca.cc.cd.cf.cg.ch.ci.ck.cl.cm.cn.co.cr.cu.cv.cw.cx.cy.cz.de.dj.dk.dm.do.dz.ec.ee.eg.er.es.et.eu.fi.fj.fk.fm.fo.fr.ga.gd.ge.gf.gg.gh.gi.gl.gm.gn.gp.gq.gr.gs.gt.gu.gw.gy.hk.hm.hn.hr.ht.hu.id.ie.il.im.in.io.iq.ir.is.it.je.jm.jo.jp.ke.kg.kh.ki.km.kn.kp.kr.kw.ky.kz.la.lb.lc.li.lk.lr.ls.lt.lu.lv.ly.ma.mc.md.me.mg.mh.mk.ml.mm.mn.mo.mp.mq.mr.ms.mt.mu.mv.mw.mx.my.mz.na.nc.ne.nf.ng.ni.nl.no.np.nr.nu.nz.om.pa.pe.pf.pg.ph.pk.pl.pm.pn.pr.ps.pt.pw.py.qa.re.ro.rs.ru.rw.sa.sb.sc.sd.se.sg.sh.si.sk.sl.sm.sn.so.sr.ss.st.sv.sx.sy.sz.tc.td.tf.tg.th.tj.tk.tl.tm.tn.to.tr.tt.tv.tw.tz.ua.ug.uk.us.uy.uz.va.vc.ve.vg.vi.vn.vu.wf.ws.ye.yt.za.zm.zw";
$countries=explode('.',$countriesStr);
$domains=["com", "co", "org", "in", "us", "gov", "mil", "int", "edu", "net", "biz", "info"];
$urls = getopt('u:',['url:']);
$url = ($urls['u'] != false)? $urls['u']: $urls['url'];
if ($url === null)
{
    $url = $argv[1];
}

if ($url === null) {
    die('no URL sent');
}

$urlParse = parse_url($url);
$urlPath=$urlParse['path'];//адрес ресурса на сервере(path)
  //if ($urlPath !== NULL) {
      $pathInf=pathinfo($urlPath);
      $urlParse['extension'] = $pathInf['extension'];

      $urlHost = $urlParse['host'];
      $explodeHost = explode('.',$urlHost);//разбиваем строку хост в массив

      $urlParse['domain'] = implode(".", $explodeHost);
      $urlParse['tld'] = end($explodeHost);
      $penUltimate = $explodeHost[count($explodeHost)-2];//предпоследний элемент массива
      if(in_array($penUltimate, $domains)){
          $urlParse['sld'] = $penUltimate.'.'.end($explodeHost);
      }
      if($urlParse['extension'] === null) {
          unset($urlParse['extension']);
      }

    $domainArr = explode('.',$urlParse['domain']);
    $countryElem = array_pop($domainArr);
    $domainElem = array_pop($domainArr);
    if(in_array($countryElem, $countries) && in_array($domainElem, $domains))
    {
        $urlParse['sld'] = $domainElem . '.' . $countryElem;
    }
    //search for subdomen
$domainArr = explode('.', $urlParse['domain']);
$remArr = array_key_exists('sld', $urlParse)? explode('.', $urlParse['sld']):[$urlParse['tld']];
$subdomainArr = array_diff($domainArr, $remArr);
array_pop($subdomainArr);
if(count($subdomainArr) > 0) {
    $subdomain = implode('.', $subdomainArr);
    $urlParse['subdomain'] = $subdomain;
}


echo json_encode($urlParse, JSON_PRETTY_PRINT);
if (array_key_exists("query", $urlParse)) {

    $urlQuery= parse_url($url,PHP_URL_QUERY);//разбиваем кьюри
    $query = [];
    parse_str($urlQuery, $query);
    fwrite(STDOUT, json_encode($query, JSON_PRETTY_PRINT));
}