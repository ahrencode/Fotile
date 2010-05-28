
<html>

<head>

<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lobster" />
<link rel="stylesheet" type="text/css" href="fotile.css" />


<script type='text/javascript' src='jquery-min.js'></script>

<script language='JavaScript'>

$(document).ready
(
    function()
    {
        //$('#urlsubmit').click( function() { loadPhoto($('#url').val(), '', 'You?'); });
        $('.tileimg').click(checkMove);
        $('#cluelink').click(function() { $('#clue').fadeIn(); });
        if( $('#url').val() != '' )
            loadPhoto($('#url').val(), '', 'You?');
        else
            loadPhoto('rogerpenrose.jpg', 'Roger Penrose', 'Wikipedia');
    }
);

var maxWidth = 600;
var maxHeight = 450;
var srcSelected = 0;
var srcTile = "";

var loadAttempts = 1;
function loadPhoto(url, title, owner)
{
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
        $('#photo').attr('src', url);
    }

    $('#count').text(0);

    var height  = $('#photo').attr('height');
    var width   = $('#photo').attr('width');
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
        logtrace('Setting margin for ('+i+','+j+') to'+'-'+x+',-'+y);
        $('#pic'+i+j).css('margin-left', '-'+x);
        $('#pic'+i+j).css('margin-top', '-'+y);
    }
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
    console.log(msg);
}

function showalert(msg)
{
    $('#alertbox').hide();
    $('#alertbox').text(msg);
    $('#alertbox').fadeIn(300);
}

var flickrPhotos;

function flickIt()
{
    if( flickrPhotos.length <= 1 )
    {
        showalert("Failed to load photos from Flickr. Sorry.");
        return;
    }

    $('#url').val('');
    idx = Math.round(100 * Math.random());
    photo = flickrPhotos[idx];
    logtrace("Using photo: "); logtrace(photo);
    url = (photo.url_o) ? photo.url_o : photo.url_m;
    loadPhoto(url, photo.title, photo.ownername+" (Flickr)");
}

function jsonFlickrApi(rsp)
{
    if (rsp.stat != "ok")
        return;

    flickrPhotos = rsp.photos.photo;
}

</script>

<script
    type="text/javascript"
    language="javascript"
    src="http://api.flickr.com/services/rest/?format=json&method=flickr.interestingness.getList&api_key=dcdaa246e88b8e12d4bb89f0a8c226a6&extras=owner_name,url_o,url_m"></script>

</head>

<body bgcolor='#555555' width='100%'>

<div id='backdrop'>
</div>

<div id='main'>
<center>

<div id='urlbox'>
    <form method='get'>
        Image URL:
        <input id='url' name='url' type='text' size='50' value='<?php print $_GET["url"]; ?>' />
        <input id='urlsubmit' type='submit' class='button' value='Tile It' />
    </form>
</div>

<table border=0 cellpadding=10 cellspacing=10>

<tr>

<td id='navcolumn'>

    <table border=0 cellpadding=10 cellspacing=10 width='100'>
        <tr> <td id='count'> </td> </tr>
        <tr> <td class='button' onClick='flickIt();'>Flick</td> </tr>
        <tr> <td class='button' onClick="initTiles();">Reset</td> </tr>
        <tr> <td id='helptext'>Click on a tile and then another, to swap them</td> </tr>
    </table>

</td>

<td>

    <table id='puzzle' cellspacing=0 cellpadding=0 border=0>

    <tr>
        <td><div><img id='pic11' class='tileimg' src=''></div></td>
        <td><div><img id='pic12' class='tileimg' src=''></div></td>
        <td><div><img id='pic13' class='tileimg' src=''></div></td>
        <td><div><img id='pic14' class='tileimg' src=''></div></td>
    </tr>

    <tr>
        <td><div><img id='pic21' class='tileimg' src=''></div></td>
        <td><div><img id='pic22' class='tileimg' src=''></div></td>
        <td><div><img id='pic23' class='tileimg' src=''></div></td>
        <td><div><img id='pic24' class='tileimg' src=''></div></td>
    </tr>

    <tr>
        <td><div><img id='pic31' class='tileimg' src=''></div></td>
        <td><div><img id='pic32' class='tileimg' src=''></div></td>
        <td><div><img id='pic33' class='tileimg' src=''></div></td>
        <td><div><img id='pic34' class='tileimg' src=''></div></td>
    </tr>

    </table>

    <div id='credits'>
    </div>

</td>
</tr>

</table>

<div id='bottombar'>
    <span id='cluelink' class='bblink'>Clue?</span>
    <span id='clue'>Click and drag any tile to see the original photograph</span> |
    <span class='bblink'><a href='http://ahren.org/code/fotile'>Download? Info?</a></span> |
    <span class='bblink'><a href='http://code.google.com/apis/webfonts/'>Fonts?</a></span>
        <small>(thank you Google!)</small>
</div>

</center>
</div>

<img id='photo' src='' />

<div id='alertbox'>
</div>
</body>
</html>

