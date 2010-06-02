<!DOCTYPE
    html
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lobster" />
<link rel="stylesheet" type="text/css" href="fotile.css" />


<script type='text/javascript' src='jquery-min.js'></script>

<script type='text/javascript'>

/* configurable stuff */
var maxWidth = 600;
var maxHeight = 450;
/* end configurable stuff */

var sourcePhotos = [];

var srcSelected = 0;
var srcTile = "";
var cellwidth = 0;
var cellheight = 0;


$(document).ready
(
    function()
    {
        $('.tileimg').click(checkMove);

        $('.bblink').click
        (
            function()
            {
                $('.bbanswer').hide();
                $(this).next('.bbanswer').fadeIn();
            }
        );

        $('#flickrhint').click( function() { $('#source').val('flickr://'); });
        $('#urlhint').click( function() { $('#source').val('http:// enter rest of URL here'); });
        $('#localhint').click
        (
            function()
            {
                $('#source').val('file:// enter file/dir path under your Fotile root');
            }
        );

        if( $('#source').val() == '' )
            loadPhoto('rogerpenrose.jpg', 'Roger Penrose', 'Wikipedia');
        else
        if( $('#source').val().substr(0, 7) == 'http://' )
            loadPhoto($('#source').val(), 'Untitled', 'You?');
        else
            nextPhoto();    // assume it is Flickr or local
    }
);

var loadAttempts = 1;
var img;
function loadPhoto(url, title, owner)
{
    $('#count').hide();
    $('#credits').hide();

    if( loadAttempts > 5 )
    {
        logtrace('Giving up on loading image.');
        showalert("Sorry. Failed to load the image. Reload this page and try again.");
        return;
    }

    showalert("Loading (attempt " + loadAttempts + "). Please Wait...");

    if( loadAttempts == 1 )
    {
        $('#puzzle').hide();
        img = new Image();
        img.src = url;
    }

    height  = img.height;
    width   = img.width;
    logtrace("URL = " + url + ", Attempt = " + loadAttempts + ", Height = " + height + ", width = " + width);

    if( height == 0 || width == 0 )
    {
        loadAttempts++;
        jsloopcall = "loadPhoto('" +url+ "','" +escape(title)+ "','"+ escape(owner)+ "')";
        logtrace("Looping to call: " + jsloopcall);
        setTimeout(""+jsloopcall, 1000);
        return;
    }

    loadAttempts = 1;
    $('#alertbox').hide();

    if( width > height && width > maxWidth )
    {
        logtrace("Sizing width down");
        height = (maxWidth/width)*height;
        width = maxWidth;
    }
    else
    if( height > maxHeight )
    {
        logtrace("Sizing height down");
        width = (maxHeight/height)*width;
        height = maxHeight;
    }

    height = Math.floor(height/3)*3;
    width = Math.floor(width/4)*4;
    logtrace("New height = " + height + ", width = " +width);

    $('#puzzle').css('height', height);
    $('#puzzle').css('width', width);
    cellheight = height / 3;
    cellwidth = width / 4;
    logtrace("Setting cell height = " + cellheight + ", width = " + cellwidth);
    $('#puzzle TD').css('width', cellwidth);
    $('#puzzle TD DIV').css('width', cellwidth);
    $('#puzzle TD').css('height', cellheight);
    $('#puzzle TD DIV').css('height', cellheight);

    $('.tileimg').each(function() { $(this).css('height', height); } );
    $('.tileimg').each(function() { $(this).css('width', width); } );
    $('.tileimg').each(function() { $(this).attr('src', url); } );

    initTiles();

    $('#puzzle').show();

    if( title == '' )
        title = "Untitled";
    if( owner == '' )
        owner = "Uknown";
    $('#credits').text("Photograph: "+unescape(title)+". By: "+unescape(owner));
    $('#credits').fadeIn();
}

function initTiles()
{
    var usedSegments = new Array();
    for(i = 0; i <= 12; i++)
        usedSegments[i] = 0;

    for(i = 1; i <= 3; i++)
        for(j = 1; j <= 4; j++)
    {
        do
        {
            segment = Math.round(11 * Math.random());
        }
        while( usedSegments[segment] == 1 )
        usedSegments[segment] = 1;
        x = (segment%4)*cellwidth;
        y = Math.floor(segment/4)*cellheight;
        xstr = (x == 0) ? "0px" : "-"+x+"px";
        ystr = (y == 0) ? "0px" : "-"+y+"px";
        logtrace('Setting margin for ('+i+','+j+') to '+xstr+','+ystr);
        $('#pic'+i+j).css('margin-left', xstr);
        $('#pic'+i+j).css('margin-top', ystr);
    }

    $('#count').text(0);
}

function checkMove()
{
    if( ! srcSelected )
    {
        srcSelected = 1;
        srcTile = $(this).attr('id');
        $(this).css('opacity', '0.5');
    }
    else
    {
        $('#count').text(parseInt($('#count').text()) + 1);
        $('#count').show(); // TODO: need to do this only once, really, not for each move!
        $('#'+srcTile).css('opacity', '1.0');
        srcSelected = 0;
        tgtTile = $(this).attr('id');
        if( tgtTile == srcTile )
            return;
        tgtX = $(this).css('margin-left');
        tgtY = $(this).css('margin-top');
        $(this).hide();
         $(this).css('margin-left', $('#'+srcTile).css('margin-left'));
         $(this).css('margin-top', $('#'+srcTile).css('margin-top'));
        $('#'+srcTile).fadeOut(300);
        $(this).fadeIn(300);
        $('#'+srcTile).css('margin-left', tgtX);
        $('#'+srcTile).css('margin-top', tgtY);
        $('#'+srcTile).fadeIn(300);
    }
}

function logtrace(msg)
{
    return;
    //console.log(msg);
}

function showalert(msg)
{
    $('#alertbox').hide();
    $('#alertbox').text(msg);
    $('#alertbox').fadeIn(300);
}

function nextPhoto()
{
    if( sourcePhotos.length < 1 )
    {
        showalert("Failed to load photos from source. Sorry.");
        return;
    }
    idx = Math.floor(sourcePhotos.length * Math.random());
    photo = sourcePhotos[idx];
    loadPhoto(photo.url, photo.title, photo.owner);
}

function jsonFlickrApi(rsp)
{
    if (rsp.stat != "ok")
        return;

    for( i in rsp.photos.photo )
    {
        photo = rsp.photos.photo[i];
        url = (photo.url_o) ? photo.url_o : photo.url_m;
        photoinfo = { url: url, title: photo.title, owner: photo.ownername+" (Flickr)" };
        sourcePhotos.push(photoinfo);
    }
}

</script>

<?php

$source = $_GET['source'];

if( preg_match("/^flickr:\/\//", $source) )
{
    print
    "
        <script
            type='text/javascript'
            src='http://api.flickr.com/services/rest/?" .
                "format=json&method=flickr.interestingness.getList&" .
                "api_key=dcdaa246e88b8e12d4bb89f0a8c226a6&" .
                "extras=owner_name,url_o,url_m'></script>
    ";
}
// ugly hacky stuff that hopefully makes this a bit more secure
elseif( preg_match("/^file:\/\/(.+)/", $source, $matches) &&
        preg_match("/^" . preg_quote(realpath("."), '/') . "\//", realpath("./".$matches[1])) )
        // try to avoid nefarious use of ".." etc to navigate out of the fotile root
        // the second check is to make sure that after all is expanded, the specified
        // path is under the fotile root
{
    $path = "./" . $matches[1];
    if( is_dir("$path") )
        foreach( preg_grep("/^.*\.(png|jpg|gif)$/", scandir("$path")) as $file )
            print
            "
                <script type='text/javascript'>
                    sourcePhotos.push({ url: '$path/$file', title: 'Untitled', owner: 'You?'});
                </script>
            ";
    else // assume its a local file
        print
        "
            <script type='text/javascript'>
                sourcePhotos.push({ url: '$path', title: 'Untitled', owner: 'You?'});
            </script>
        ";
}

?>

</head>

<body>

<div id='backdrop'>
</div>

<div id='main'>
<center>

<div id='sourcebox'>
    <form method='get'>
        Source:
        <input id='source' name='source' type='text' size='50'
            value='<?php global $source; print $source; ?>' />
        <input type='submit' class='button' value='Load It' />
        <div class='smallstuff' style='margin-top: 10px;'>
            (source can be a 
             <span id='urlhint' class='hintlink'>URL to an image</span>,
             relative <span id='localhint' class='hintlink'>path to a local file or directory</span>
             with image files, or 
             <span id='flickrhint' class='hintlink'>Flickr</span>)
        </div>
    </form>
</div>

<table cellpadding='10' cellspacing='10'>

<tr>

<td id='navcolumn'>

    <table cellpadding='10' cellspacing='10' width='100'>
        <tr> <td id='count'> </td> </tr>
        <tr> <td class='button' onClick='nextPhoto();'>Flick</td> </tr>
        <tr> <td class='button' onClick="initTiles();">Reset</td> </tr>
        <tr> <td id='helptext'>Click on a tile and then another, to swap them</td> </tr>
    </table>

</td>

<td>

    <table id='puzzle' cellspacing='0' cellpadding='0'>

    <tr>
        <td><div><img id='pic11' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic12' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic13' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic14' class='tileimg' alt='' src='' /></div></td>
    </tr>

    <tr>
        <td><div><img id='pic21' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic22' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic23' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic24' class='tileimg' alt='' src='' /></div></td>
    </tr>

    <tr>
        <td><div><img id='pic31' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic32' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic33' class='tileimg' alt='' src='' /></div></td>
        <td><div><img id='pic34' class='tileimg' alt='' src='' /></div></td>
    </tr>

    </table>

    <div id='credits'>
    </div>

</td>
</tr>

</table>

<div id='bottombar' class='smallstuff'>
    <span class='bblink hintlink'>Clue?</span>
    <span class='bbanswer'>Click and drag any tile to see the original photograph</span> |
    <span class='bblink hintlink'><a href='http://ahren.org/code/fotile'>Download? Info?</a></span> |
    <span class='bblink hintlink'>Browsers?</span>
    <span class='bbanswer'>Firefox 3.5+, Safari 3+, Chrome 4+, IE 7+</span> |
    <span class='bblink hintlink'><a href='http://code.google.com/apis/webfonts/'>Fonts?</a></span>
        <small>(thank you Google!)</small>
</div>

</center>
</div>

<div id='alertbox'>
</div>
</body>
</html>

