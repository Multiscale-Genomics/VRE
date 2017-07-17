<?php
/*
 * libForm
 * Gestio de formularis
 * versio PHP
 */
function getInput($nom, $m, $val, $size, $extra='') {
    if ($m)
        return "<input name='$nom' $extra value='$val' size='$size'>";
    else
        return $val;
}
function getPassword($nom, $m, $val, $size, $extra='') {
    if ($m)
        return "<input type='password' name='$nom' $extra value='$val' size='$size'>";
    else
        return $val;
}

function getHidden($nom, $m, $val, $extra='') {
    if ($m)
        return "<input type='hidden' name='$nom' $extra value='$val' size='$size'>";
    else
        return '';
}

function getTextArea($nom, $m, $val, $c, $r, $extra='') {
    if ($m)
        return "<textarea $extra wrap='virtual' name='$nom' rows='$r' cols='$c'>$val</textarea>";
    else
        return $val;
}

function getSelect($nom, $m, &$dicc, $val, $mult = 0, $size = 5, $deftxt = '', $extra ='') {
    $outsel = '';
    $val0 = array();
    if ($mult) {
        foreach ((array)$val as $i)
            $val0[$i]=1;
    }
    if ($m) {
        $outsel= "<select $extra ";
        if ($mult)
            $outsel .= " multiple size='$size' name='$nom"."[]'>";
        else
            $outsel .= " name='$nom'>";
        if ($deftxt) {
            $outsel .= "<option value='XX'";
            if (($val == "") or ($val == "XX"))
                $outsel .= " selected ";
            $outsel .= ">$deftxt";
        }
        if ($dicc) {
            foreach ($dicc as $k => $v) {
                $outsel .= "<option value='$k'";
                if (($val == $k) or ($mult and $val0[$k]))
                    $outsel .= " selected ";
                $outsel .= ">$v";
            }
        }
        $outsel .= "</select>";
    } else {
        if ($mult) {
            $vals = Array();
            foreach ((array)$val as $i)
                $vals[] = $dicc[$i];
            $outsel = join (", ", $vals);
        } else
            $outsel = $dicc[$val];
    }
    return $outsel;
}

function getCBox($nom, $m, $valor, $txt, $esq = False, $extra='') {
    $tt = "";
    if ($m) {
        $tt = " <input name='$nom' $extra type='checkbox' ";
        if ($valor == "on")
            $tt .= "checked";
        $tt .= "> ";
        if ($esq)
            return $tt.$txt;
        else
            return $txt.$tt;
    } else
        if ($valor == "on")
            return $txt;
}

function getCBoxBool($nom, $m, $valor, $txt, $esq = False) {
    return getCBox01($nom, $m, $valor, $txt, $esq);
}

#
# innecessaria function getCBoxBool (nom,m,valor,txt,esq)
#
function getCBox01($nom, $m, $valor, $txt, $esq = False) {
    $tt = "";
    if ($m) {
        $tt = " <input name='$nom' type='checkbox' ";
        if ($valor)
            $tt .= "checked";
        $tt .= "> ";
        if ($esq)
            return $tt.$txt;
        else
            return $txt.$tt;
    } else {
        if ($valor and $txt)
            return $txt;
        if (!$txt)
            return $valor;
    }
}

function getRBut($nom, $m, $valor, &$dicc, $vert = False) {
    $tt = "";
    if ($m) {
        foreach ($dicc as $k => $v) {
            $tt .= "<input type='radio' name='$nom' value='$k' " ;
            if ($valor == $k)
                $tt .= " checked ";
            $tt .= "> $v";
            if ($vert)
                $tt .= "<br>";
        }
    } else
        $tt = $dicc[$valor];
    return $tt;
}

function get1RBut($nom, $m, $valor, $k) {
    $tt = "";
    if ($m) {
        $tt .= "<input type='radio' name='$nom' value='$k' ";
        if ($valor==$k)
            $tt.= " checked ";
        $tt .= "> ";
    } else
        $tt = $valor;
    return $tt;
}

function getSiNo($nom, $m, $valor, $vert = False) {
    $dicc = array('on' => 'Si', 'off' => 'No');
    return getRBut($nom, $m, $valor, $dicc, $vert);
}

function getFile ($nom, $m, $val, $size, $extra='') {
    if ($m)
        return "<input type='file' name='$nom' $extra value='$val' size='$size'>";
    else
        return $val;
}
//===========================================================