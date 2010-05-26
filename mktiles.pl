#!/opt/local/bin/perl
#

$root = "~/Desktop/tmp/tiles";

$index = shift;
if( $index !~ /^\d+$/ )
{
    print "Missing arg: starting index number for dir names.\n";
    exit;
}

chop(@imgs = `ls $root`);

foreach my $img ( @imgs )
{
    next unless( -f $root/$img );
    print "... $img\n";
    mkdir("tiles$index") || die("$!\n");
    print "...... resizing image\n";
    system("convert -resize 640x480 $root/$img $root/$img") && die("$!\n");
    print "...... generating thumbnail\n";
    system("convert -resize 64x64 $root/$img tiles$index/thumbnail.jpg") && die("$!\n");
    print "...... generating tiles\n";
    system("convert $root/$img -crop 160x160 tiles$index/tiles_\%d.jpg") && die("$!\n");
    $index++;
}


