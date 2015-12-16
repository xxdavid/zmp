#!/usr/bin/php
<?php
/*
 * texy2latex
 * Copyright (C) 2007  Jan Breuer
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Jan Breuer <honza.breuer@gmail.com>
 *
 */


require_once dirname(__FILE__)."/libs/html2latex.php";
require_once dirname(__FILE__)."/libs/texy.php";

class Texy2Latex {

    private $texy;
    private $html2latex;

    public function __construct() {
        $this->texy = new Texy();
        $this->texy->allowed['phrase/sup'] = TRUE;
        $this->texy->allowed['phrase/sub'] = TRUE;
        $this->texy->allowed['phrase/cite'] = TRUE;


        $this->texy->allowed['my/latex'] = TRUE;


        // add new syntax: $latex$
        $this->texy->registerLinePattern(
            array($this, 'latexHandler'),
            '#(?<!\$)\$(?![\s*])(.+)'.TEXY_MODIFIER.'?(?<![\s*])\$(?!\$)'.TEXY_LINK.'??()#Uus',
            'my/latex'
        );


        $this->html2latex = new Html2Latex();
        //$this->html2latex->setParam($key, $value);
    }

    public function convert($text) {
        $text = str_replace('%', '\%', $text);
        $text = $this->texy->process($text);
        $text = $this->html2latex->convert($text);
        return $text;
    }

    /**
    * Pattern handler for my syntaxes
    *
    * @param TexyLineParser
    * @param array   reg-exp matches
    * @param string  pattern name (mySyntax1 or mySyntax2)
    * @return TexyHtml|string
    */
    function latexHandler($parser, $matches, $name) {
        list(, $mContent) = $matches;
        return $this->texy->protect(Texy::escapeHtml('$'.$mContent.'$'), Texy::CONTENT_TEXTUAL);
    }


}

$texy2latex = new Texy2Latex();
$sourceFile = __DIR__ . '/../content.texy';
$source = file_get_contents($sourceFile);
$latex = $texy2latex->convert($source);
$latexFile = __DIR__ . '/../latex/zmp.tex';
file_put_contents($latexFile, $latex);

?>
