<?php
/************** blockchain 101 *****************/
/***********************************************/
/** Isaac Jacobs Krishna Deilson Christopher ***/
/***********************************************/
/***** GNU General Public License v3 - GPLv3 ***/
/***********************************************/
$GLOBALS['blockchainfile'] = "blockchain.file";
$GLOBALS['filehandle'] = NULL;

if(empty($_POST) || !isset($_POST['content']) || $_POST['content'] == ""){
    showForm();
}else{
    $block = addToBlockChain();
    displayNewBlock($block);
}

function showForm(){
    echo <<<form
<form action="" method="post">
<label for="content">Add text to the block chain:</label>
<input id="content" name="content" type="text">
<button type="submit" value="1">Add</button>
form;
}

function getPreviousHash(){
    if(!$GLOBALS['filehandle'] = fopen($GLOBALS['blockchainfile'],"a+b")) die("unable to get file handle.");
    flock($GLOBALS['filehandle'],LOCK_EX);
    if(is_file($GLOBALS['blockchainfile']) && filesize($GLOBALS['blockchainfile']) > 0){
        do{
            $block = fgets($GLOBALS['filehandle']);
        }while(!feof($GLOBALS['filehandle']));
        return hash("sha1",$block);
    }else{
        return hash("sha1","EMDM");
    }
}

function addToBlockChain(){
    $hash = getPreviousHash();
    $data = encode(trim($_POST['content']));
    $block = ["time"=>time(),"content"=>$data,"PreviousBlockHash"=>$hash];
    $block = json_encode($block);
    fwrite($GLOBALS['filehandle'],$block.PHP_EOL);
    flock($GLOBALS['filehandle'],LOCK_UN);
    return $block;
}

function displayNewBlock($block){
    $a = json_decode($block);
    $a->content = decode($a->content);
    echo "<h3>A new block was added to the blockchain.</h3>";
    echo "<h5>Block details:</h5>";
    foreach($a as $k => $v){
        echo "<pre>{$k} : {$v}</pre>";
    }
}

function decode($string){
  return zlib_decode(hex2bin($string));
}

function encode($string){
  return bin2hex(zlib_encode($string, ZLIB_ENCODING_RAW));
}
