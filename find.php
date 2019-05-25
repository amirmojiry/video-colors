<?php

echo shell_exec("ffmpeg -i sample.mkv -vf 'select=eq(pict_type\,I)' -vsync vfr sample/sample%d.jpg -hide_banner");


ini_set('max_execution_time', 300); //300 seconds = 5 minutes
$start = microtime(true);

$fi = new FilesystemIterator (__DIR__."/sample", FilesystemIterator::SKIP_DOTS);
$all_frames = iterator_count ($fi);


$red_m = $green_m = $blue_m = 0;
for ($i = 1; $i <= $all_frames; $i++) {
	$img = "sample/sample$i.jpg";
	$imgHand = imagecreatefromjpeg($img);
	$imgSize = GetImageSize ($img);
	$imgWidth = $imgSize[0];
	$imgHeight = $imgSize[1];
	$red = $green = $blue = 0;
	for ($l = 0; $l < $imgHeight; $l++) {
		for ($c = 0; $c < $imgWidth; $c++) {
			$pixelColor = imagecolorat ($imgHand, $c, $l);
			$colors = imagecolorsforindex($imgHand, $pixelColor);
			$red += $colors ['red'];
			$green += $colors ['green'];
			$blue += $colors ['blue'];
		}
	}
	$sum_pixels = $imgWidth*$imgHeight;
	$red_m_f = round($red/$sum_pixels);
	$red_m += $red_m_f/$all_frames;
	$green_m_f = round($green/$sum_pixels);
	$green_m += $green_m_f/$all_frames;
	$blue_m_f = round($blue/$sum_pixels);
	$blue_m += $blue_m_f/$all_frames;
	$until_now = microtime(true) - $start;
	echo "<div style='background-color:rgb($red_m_f, $green_m_f, $blue_m_f)'>
	Average color of image number $i - time $until_now</div>";
}
$end = microtime(true);
$finish = $end - $start;
$red_m = round ($red_m);
$green_m = round ($green_m);
$blue_m = round ($blue_m);
echo "<br>";
echo "<div style='background-color:rgb($red_m, $green_m, $blue_m)'>
	Average color of video - time $finish</div>";
