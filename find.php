<?php

ini_set('max_execution_time', 1200); //1200 seconds = 20 minutes

$start = microtime(true);

/**
* for every frames:
* ffmpeg -i [video_name.video_ext] frames/sample%d.jpg -hide_banner
* for every seconds:
* ffmpeg -i [video_name.video_ext] -vf fps=1 frames/sample%d.jpg -hide_banner
* for every keyframes:
* ffmpeg -i [video_name.video_ext] -vf 'select=eq(pict_type\,I)'
* 			 -vsync vfr frames/sample%d.jpg -hide_banner
*/
echo shell_exec("ffmpeg -i sample.mkv -vf 'select=eq(pict_type\,I)' -vsync vfr frames/sample%d.jpg -hide_banner");

$fi = new FilesystemIterator (__DIR__."/frames", FilesystemIterator::SKIP_DOTS);
$all_frames = iterator_count ($fi);


$red_m = $green_m = $blue_m = 0;
for ($i = 1; $i <= $all_frames; $i++) {
	$img = "frames/sample$i.jpg";
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
	$until_now = round(microtime(true) - $start);
	echo "<span style='background-color:rgb($red_m_f, $green_m_f, $blue_m_f)'>
	$i - $until_now</span>";
}
$finish = round(microtime(true) - $start);
$red_m = round ($red_m);
$green_m = round ($green_m);
$blue_m = round ($blue_m);
echo "<br>";
echo "<div style='background-color:rgb($red_m, $green_m, $blue_m)'>
	Average color of video - time $finish seconds</div>";
