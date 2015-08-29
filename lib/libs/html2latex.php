<?php #:mode=php:folding=explicit:collapseFolds=1:

/**
 *
 * Html2Latex
 * modified version texy2latex
 * Copyrignt (C) 2007 Jan Breuer <honza tecka breuer zavinac gmail tecka com>
 * 
 */

/**
 *
 * Html2texy
 *
 * Copyright (C) 2007 Jakub Roztocil <jakub uzenac webkitchen tecka cz>
 *
 * {{{ Licence
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * }}}
 *
 * Usage:
 *
 * $h2l = new Html2Latex();
 * $h2l->setParam($key, $value);
 * echo $h2l->convert($html);
 *
 */

class Html2Latex {

    private $config = array (
        'ignore-empty-divs' => true,
        'ignore-all-divs' => false
    );

    private $proc;

    public function __construct() {
        $this->proc = new XSLTProcessor();
        $this->proc->registerPHPFunctions();
        $style = new DOMDocument('1.0', 'utf-8');
        $style->load(dirname(__FILE__) . '/../resources/html2latex.xsl');
        $this->proc->importStylesheet($style);
        foreach ($this->config as $k => $v) {
            $this->proc->setParameter('', $k, $v);
        }
    }

    public function convert($html) {
        // TODO: charset
        $html = $this->prepareHtml($html);
        $htmlDoc = new DOMDocument();
        @$htmlDoc->loadHTML($html);
        $text = $this->proc->transformToXml($htmlDoc);
        return $this->applyTemplate($text);
    }

    public function setParam($k, $v) {
        $this->proc->setParameter('', $k, $v);
    }

    private function prepareHtml($html) {
        // remove doctype, namespaces
        $html = preg_replace('/xmlns=[\'"].+[\'"]|<!DOCTYPE[^>]>|<html[^>]+>/', '', $html);
        $html = '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>' . $html;

        // remove some entities
        $find =    array("&nbsp;" ,"&#160;" ,"\xc2\xa0" ,"\xe2\x86\x92"   ,"\xc2\xad");
        $replace = array("~"      ,"~"      ,"~"        ,"$\\rightarrow$" ,"");
        $html = str_replace($find,$replace,$html);
        return $html;
    }

    private function applyTemplate($text) {
        $template = file_get_contents(dirname(__FILE__) . '/../../template.tex');
        return preg_replace('/%<<<paste body here>>>/', $text, $template); // preg_replace z nejakeho duvodu vuhodnocuje i $template, takze nahradi "promenne" $neco za jejich obsah, coz je nesmysl
        // return preg_replace('(%<<<paste body here>>>)', $text, $template);
    }

}

?>
