
<html>

<head>

<link href="tiles.css" rel="stylesheet" type="text/css">

<?php

global $HTTP_GET_VARS;

$imgdir = $HTTP_GET_VARS['puzzle'];
$next   = get_next($imgdir);
if( $imgdir != "" )
    init_images($imgdir);
else
    print "
        <script language='JavaScript'>
        var noPuzzle = 1;
        </script>
        ";

function get_next($imgdir)
{
    $tiledirs = get_dir_entries(".", "d");
    for($ctr = 0; $imgdir != "" && $imgdir != $tiledirs[$ctr] && $ctr < count($tiledirs); $ctr++)
        ;
    if( $ctr >= (count($tiledirs)-1) )
        $ctr = 0;
    else
        $ctr++;
    return($tiledirs[$ctr]);
}

function init_images($imgdir)
{
    print "
        <script language='JavaScript'>
        var tileDir = '$imgdir';
        var pics = new Array(
    ";

    $images = get_dir_entries($imgdir, "f");
    $first = 1;
    foreach( $images as $image )
    {
        if( preg_match("/^thumbnail.jpg$/", $image) )
            continue;
        if( ! $first )
            print ",\n";
        $first = 0;
        print "\t\t'$image'";
    }

    print "
        );
        </script>
        ";
}

function get_dir_entries($dir, $type)
{
    if( ! ( $dh = opendir($dir) ) )
    {
        print "Could not scan directory ($dir)!\n";
        exit;
    }

    $entries = array();
    while( ($entry = readdir($dh)) !== false )
    {
        if( $entry == "." || $entry == ".." )
            continue;
        if( $type == "d" && ! is_dir("$dir/$entry") )
            continue;
        if( $type == "f" && ! is_file("$dir/$entry") )
            continue;

        array_push($entries, $entry);
    }

    closedir($dh);

    natsort($entries);
    return($entries);
}


function init_chooser()
{
    $tiledirs = get_dir_entries(".", "d");
    print "<table border=0 cellpadding=0 cellspacing=20>\n<tr>\n";
    $ctr = 0;
    foreach( $tiledirs as $dir )
    {
        if( $ctr > 0 && $ctr % 6 == 0 )
            print "</tr>\n<tr>\n";
        print "
            <td>
                <a href='?puzzle=$dir'><img class='chooserpic' src='$dir/thumbnail.jpg'></a>
            </td>
            ";
        $ctr++;
    }
    print "</tr></table>\n";
}

?>

<script language='JavaScript'>

var srcSelected = 0;
var srcTile = "";
var noPuzzle;

function initTiles()
{
    srcSelected = 0;

    if( noPuzzle == 1 )
    {
        showChooser();
        return;
    }

    var availImages = new Array();
    for(i = 0; i <= 12; i++)
        availImages[i] = 1;

    for(i = 1; i <= 3; i++)
        for(j = 1; j <= 4; j++)
    {
        img = 0;
        while( availImages[img] != 1 )
            img = Math.round(11 * Math.random());
        availImages[img] = 0;
        document.images["pic"+i+j].src = tileDir + "/" + pics[img];
    }
}


function showChooser()
{
    document.getElementById('chooser').style.visibility = 'visible';
}

function checkMove(tile)
{
    if( ! srcSelected )
    {
        srcSelected = 1;
        srcTile = tile.name;
        flashImage(tile.name);
    }
    else
    {
        srcSelected = 0;
        var tmptile = document.images[srcTile].src;
        document.images[srcTile].src = tile.src;
        document.images[srcTile].style.border = 'none';
        tile.src = tmptile;
    }
}

function flashImage(name)
{
    var tile = document.images[name];

    if( srcSelected == 0 )
    {
        tile.style.visibility = 'visible';
        return;
    }

	( tile.style.visibility == 'visible' ) ?
        tile.style.visibility = 'hidden' :
        tile.style.visibility = 'visible';

	setTimeout('flashImage("' + name + '")', 500);
}


</script>

</head>

<body bgcolor='#555555' width='100%' onLoad='initTiles();'>
<center>

<br/>

<?php
if( $imgdir != "" )
{
?>

<table border=0 cellpadding=10 cellspacing=10>

<tr>

<td id='navcolumn'>
    <table border=0 cellpadding=10 cellspacing=10 width='100'>
    <tr>
    <td class='button' onClick="initTiles();">
        Reset
    </td>
    </tr>
    <tr>
    <td class='button' onClick='document.location = "?puzzle=<?php print $next; ?>";'>
        Next
    </td>
    </tr>
    <tr>
    <td class='button' onClick='showChooser();'>
        Choose
    </td>
    </tr>
    <tr>
        <td> <br/><br/> </td>
    </tr>
    <tr>
    <td id='helptext'>
        Click on a tile and then another, to swap them.
    </td>
    </tr>
    </table>
</td>

<td>

    <table id='puzzle' cellspacing=0 cellpadding=0 border=0>

    <tr>
        <td><img name='pic11' src='' onClick='checkMove(this);'></td>
        <td><img name='pic12' src='' onClick='checkMove(this);'></td>
        <td><img name='pic13' src='' onClick='checkMove(this);'></td>
        <td><img name='pic14' src='' onClick='checkMove(this);'></td>
    </tr>

    <tr>
        <td><img name='pic21' src='' onClick='checkMove(this);'></td>
        <td><img name='pic22' src='' onClick='checkMove(this);'></td>
        <td><img name='pic23' src='' onClick='checkMove(this);'></td>
        <td><img name='pic24' src='' onClick='checkMove(this);'></td>
    </tr>

    <tr>
        <td><img name='pic31' src='' onClick='checkMove(this);'></td>
        <td><img name='pic32' src='' onClick='checkMove(this);'></td>
        <td><img name='pic33' src='' onClick='checkMove(this);'></td>
        <td><img name='pic34' src='' onClick='checkMove(this);'></td>
    </tr>

    </table>

</table>

<?php
}
?>

<div id='chooser'>
    <center>
    <h1>Choose a Puzzle</h1>
    <?php init_chooser(); ?>
    </center>
</div>

<br/>

</center>
</body>
</html>

